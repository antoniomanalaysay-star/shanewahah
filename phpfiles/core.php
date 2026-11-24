<?php
/**
 * AtClam PHP UI - shared core.
 * Talks to the existing FastAPI backend used by the Flutter app.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const BACKEND_DEFAULT = 'http://localhost:8000';

class ApiException extends Exception
{
    public int $status;

    public function __construct(string $message, int $status = 0)
    {
        parent::__construct($message);
        $this->status = $status;
    }
}

function backend_url(): string
{
    $url = $_COOKIE['atclam_backend'] ?? (getenv('API_BASE_URL') ?: BACKEND_DEFAULT);
    $url = trim((string)$url);
    if ($url === '') {
        $url = BACKEND_DEFAULT;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'http://' . $url;
    }
    return rtrim($url, '/');
}

function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function api_call(string $method, string $path, $body = null, ?string $token = null): array
{
    $url = backend_url() . (str_starts_with($path, '/') ? $path : '/' . $path);
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    if ($body !== null && strtoupper($method) !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
    }
    $raw = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new ApiException(
            "Can't reach the server. Check your connection and the server address in the account menu." .
            ($error ? ' (' . $error . ')' : ''), 0
        );
    }
    $data = json_decode($raw, true);
    return [$status, $data];
}

function api_error_message($data, int $status): string
{
    if (is_array($data)) {
        if (isset($data['detail'])) {
            $d = $data['detail'];
            if (is_string($d)) {
                return $d;
            }
            if (is_array($d)) {
                $parts = [];
                foreach ($d as $item) {
                    $parts[] = is_array($item) && isset($item['msg'])
                        ? str_replace('Value error, ', '', (string)$item['msg'])
                        : (is_scalar($item) ? (string)$item : json_encode($item));
                }
                return implode("\n", $parts);
            }
        }
        if (isset($data['error']) && is_string($data['error'])) {
            return $data['error'];
        }
        if (isset($data['message']) && is_string($data['message'])) {
            return $data['message'];
        }
    }
    return 'Something went wrong (error ' . $status . ').';
}

/** API call that throws a user-friendly exception. */
function api(string $method, string $path, $body = null, ?string $token = null)
{
    [$status, $data] = api_call($method, $path, $body, $token ?? ($_SESSION['token'] ?? null));
    if ($status >= 200 && $status < 300) {
        return $data;
    }
    throw new ApiException(api_error_message($data, $status), $status);
}

/** Non-fatal API call used for optional UI data such as the notification bell. */
function safe_api(string $method, string $path, $body = null)
{
    try {
        return api($method, $path, $body);
    } catch (Throwable $e) {
        return null;
    }
}

function current_user(bool $refresh = false): ?array
{
    if (empty($_SESSION['token'])) {
        return null;
    }
    if ($refresh || empty($_SESSION['user'])) {
        try {
            $_SESSION['user'] = api('GET', '/auth/me');
        } catch (ApiException $e) {
            if ($e->status === 401 || $e->status === 403) {
                session_destroy();
                return null;
            }
            $_SESSION['api_error'] = $e->getMessage();
        }
    }
    return $_SESSION['user'] ?? null;
}

function require_user(): array
{
    $user = current_user(true);
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_user();
    if (!in_array($user['role'] ?? '', $roles, true)) {
        redirect('index.php');
    }
    return $user;
}

function login_user(array $data): void
{
    $_SESSION['token'] = $data['access_token'] ?? '';
    $_SESSION['user'] = $data['user'] ?? null;
    unset($_SESSION['api_error']);
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function flash(string $message, bool $error = false): void
{
    $_SESSION['flash'] = ['message' => $message, 'error' => $error];
}

function take_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Single place for form actions: returns [success, message, data]. */
function perform_action(callable $callback): array
{
    try {
        $data = $callback();
        return [true, '', $data];
    } catch (ApiException $e) {
        return [false, $e->getMessage(), null];
    } catch (Throwable $e) {
        return [false, $e->getMessage(), null];
    }
}

function input(string $key, string $default = ''): string
{
    return trim((string)($_POST[$key] ?? $default));
}

function query(string $key, string $default = ''): string
{
    return trim((string)($_GET[$key] ?? $default));
}

function valid_time(string $value): string
{
    return preg_match('/^([01]?\d|2[0-3]):[0-5]\d$/', $value) ? $value : '';
}

function data_uri_from_upload(array $file, int $maxBytes = 12000000): ?string
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || ($file['error'] ?? 1) !== 0) {
        return null;
    }
    if (filesize($file['tmp_name']) > $maxBytes) {
        return null;
    }
    $bytes = file_get_contents($file['tmp_name']);
    if ($bytes === false) {
        return null;
    }
    $mime = function_exists('mime_content_type') ? (string)mime_content_type($file['tmp_name']) : 'image/jpeg';
    if (!str_starts_with($mime, 'image/')) {
        $mime = 'image/jpeg';
    }
    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
}
