<?php
/**
 * Small JSON proxy for browser JavaScript.
 * It forwards requests to the FastAPI backend and silently attaches the
 * bearer token stored in the PHP session (the token never reaches JS).
 */
require_once __DIR__ . '/core.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$path = trim((string)($_GET['path'] ?? ''));
$method = strtoupper(trim((string)($_GET['method'] ?? 'GET')));
if ($path === '' || !str_starts_with($path, '/')) {
    http_response_code(400);
    echo json_encode(['detail' => 'Invalid API path.']);
    exit;
}
if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
    http_response_code(405);
    echo json_encode(['detail' => 'Method not allowed.']);
    exit;
}

$raw = file_get_contents('php://input');
$body = null;
if ($raw !== '' && $raw !== false) {
    $decoded = json_decode($raw, true);
    $body = is_array($decoded) ? $decoded : [];
}

try {
    [$status, $data] = api_call($method, $path, $body, $_SESSION['token'] ?? null);
    http_response_code($status ?: 500);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode(['detail' => $e->getMessage()]);
}
