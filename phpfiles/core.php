<?php
/**
 * AtClam PHP UI - self-contained demo core.
 *
 * No server, no database, no cURL. All API calls are answered locally from a
 * small demo state kept only in the PHP session, so every screen can be tested
 * by opening the PHP files directly on a local PHP server.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const BACKEND_DEFAULT = 'http://localhost:8000';
const DEMO_TOKEN_PREFIX = 'demo:';

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
    $url = trim((string)($_COOKIE['atclam_backend'] ?? (getenv('API_BASE_URL') ?: BACKEND_DEFAULT)));
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

/* -------------------------------------------------------------------------- */
/* Demo data                                                                  */
/* -------------------------------------------------------------------------- */

function demo_seed(): array
{
    $today = date('Y-m-d');
    $users = [
        [
            'id' => 1, 'name' => 'ThirdyAdmin', 'full_name' => 'ThirdyAdmin',
            'username' => 'ThirdyAdmin', 'email' => 'admin@atclam.local',
            'role' => 'admin', 'school_id' => 'ADMIN-001', 'section' => '',
            'student_id' => null, 'has_face' => false, 'is_active' => true,
        ],
        [
            'id' => 2, 'name' => 'Prof. Maria Santos', 'full_name' => 'Prof. Maria Santos',
            'username' => 'instructor', 'email' => 'instructor@atclam.local',
            'role' => 'instructor', 'school_id' => 'INS-001', 'section' => '',
            'student_id' => null, 'has_face' => false, 'is_active' => true,
        ],
        [
            'id' => 3, 'name' => 'Juan Dela Cruz', 'full_name' => 'Juan Dela Cruz',
            'username' => 'student', 'email' => 'student@atclam.local',
            'role' => 'student', 'school_id' => 'STU-001', 'section' => 'Grade 11 A',
            'student_id' => 'face001', 'has_face' => true, 'is_active' => true,
        ],
        [
            'id' => 4, 'name' => 'Jane Doe', 'full_name' => 'Jane Doe',
            'username' => 'jane', 'email' => 'jane@atclam.local',
            'role' => 'student', 'school_id' => 'STU-002', 'section' => 'Grade 11 A',
            'student_id' => 'face002', 'has_face' => true, 'is_active' => true,
        ],
        [
            'id' => 5, 'name' => 'Alex Rivera', 'full_name' => 'Alex Rivera',
            'username' => 'alex', 'email' => 'alex@atclam.local',
            'role' => 'student', 'school_id' => 'STU-003', 'section' => 'Grade 11 B',
            'student_id' => 'face003', 'has_face' => true, 'is_active' => true,
        ],
    ];

    $classes = [
        [
            'id' => 'a1b2c3d4', 'code' => 'A1B2C3', 'name' => 'Mathematics 101',
            'description' => 'Algebra, functions and problem solving.', 'section' => 'Grade 11 A',
            'instructor_id' => 2, 'present_time' => '08:00', 'late_time' => '08:15',
            'end_time' => '09:00', 'created_at' => time() - 86400 * 7,
        ],
        [
            'id' => 'b2c3d4e5', 'code' => 'B2C3D4', 'name' => 'Science 101',
            'description' => 'General science laboratory class.', 'section' => 'Grade 11 B',
            'instructor_id' => 2, 'present_time' => '10:00', 'late_time' => '10:15',
            'end_time' => '11:00', 'created_at' => time() - 86400 * 3,
        ],
    ];

    $announcements = [
        'a1b2c3d4' => [
            ['id' => 1, 'title' => 'Welcome to Math 101', 'body' => 'Bring your notebook and scientific calculator to every class.',
                'author' => 'Prof. Maria Santos', 'date' => date('M d, Y H:i')],
        ],
        'b2c3d4e5' => [],
    ];

    $attendance = [];
    foreach (['a1b2c3d4', 'b2c3d4e5'] as $ci => $cid) {
        foreach ([3, 4, 5] as $ui => $uid) {
            if ($ci === 1 && $uid === 5) {
                continue;
            }
            for ($day = 2; $day <= (int)date('d'); $day += 3) {
                $date = sprintf('%s-%02d-%02d', date('Y'), (int)date('m'), $day);
                $statuses = ['present', 'late', 'present', 'absent'];
                $attendance[$cid][$uid][$date] = [
                    'status' => $statuses[($day + $uid + $ci) % 4],
                    'time' => sprintf('%02d:%02d:%02d', 8 + $ci, 5 + ($day % 20), ($day * 7) % 60),
                ];
            }
        }
    }

    return [
        'users' => $users,
        'classes' => $classes,
        'enrollments' => [
            'a1b2c3d4' => [3, 4, 5],
            'b2c3d4e5' => [3, 4],
        ],
        'announcements' => $announcements,
        'excuses' => [
            ['class_id' => 'a1b2c3d4', 'user_id' => 3, 'date' => $today, 'note' => 'Doctor’s appointment.', 'name' => 'Juan Dela Cruz'],
        ],
        'attendance' => $attendance,
        'session' => ['active' => false, 'class_id' => null, 'started' => '', 'id' => 0],
        'notifications' => [
            ['id' => 1, 'kind' => 'announcement', 'title' => 'Welcome to Math 101',
                'body' => 'Your instructor posted a new announcement.', 'class_id' => 'a1b2c3d4',
                'class_name' => 'Mathematics 101', 'time' => date('M d, H:i'), 'read' => false, 'user_id' => 3],
            ['id' => 2, 'kind' => 'excuse', 'title' => 'Excuse letter · Juan Dela Cruz',
                'body' => $today . ': Doctor’s appointment.', 'class_id' => 'a1b2c3d4',
                'class_name' => 'Mathematics 101', 'time' => date('M d, H:i'), 'read' => true, 'user_id' => 2],
        ],
        'next_user_id' => 6,
        'next_announcement_id' => 2,
        'next_notification_id' => 3,
        'next_session_id' => 1,
    ];
}

function &demo_state(): array
{
    if (!isset($_SESSION['demo']) || !is_array($_SESSION['demo'])) {
        $_SESSION['demo'] = demo_seed();
    }
    return $_SESSION['demo'];
}

function demo_public(array $u): array
{
    return [
        'id' => $u['id'] ?? 0,
        'name' => $u['name'] ?? $u['full_name'] ?? '',
        'full_name' => $u['full_name'] ?? $u['name'] ?? '',
        'username' => $u['username'] ?? '',
        'email' => $u['email'] ?? '',
        'role' => $u['role'] ?? 'student',
        'school_id' => $u['school_id'] ?? '',
        'section' => $u['section'] ?? '',
        'student_id' => $u['student_id'] ?? null,
        'has_face' => (bool)($u['has_face'] ?? false),
        'is_active' => (bool)($u['is_active'] ?? true),
    ];
}

function demo_user_by_id($id): ?array
{
    $s =& demo_state();
    foreach ($s['users'] as $u) {
        if ((int)($u['id'] ?? 0) === (int)$id) {
            return $u;
        }
    }
    return null;
}

function demo_user_by_token(?string $token): ?array
{
    if (!is_string($token) || !preg_match('/^' . preg_quote(DEMO_TOKEN_PREFIX, '/') . '(\d+)$/', $token, $m)) {
        return null;
    }
    return demo_user_by_id((int)$m[1]);
}

function demo_role(?string $token): string
{
    $u = demo_user_by_token($token);
    return (string)($u['role'] ?? '');
}

function demo_find_class($id): ?array
{
    $s =& demo_state();
    foreach ($s['classes'] as $c) {
        if (($c['id'] ?? '') === (string)$id) {
            return $c;
        }
    }
    return null;
}

function demo_enrolled($cid, $uid): bool
{
    $s =& demo_state();
    return in_array((int)$uid, array_map('intval', $s['enrollments'][$cid] ?? []), true);
}

function demo_manages(array $user, array $class): bool
{
    return ($user['role'] ?? '') === 'admin'
        || (($user['role'] ?? '') === 'instructor' && (int)($class['instructor_id'] ?? 0) === (int)($user['id'] ?? 0));
}

function demo_class_public(array $c): array
{
    $s =& demo_state();
    $owner = demo_user_by_id($c['instructor_id'] ?? 0);
    return [
        'id' => $c['id'] ?? '',
        'code' => $c['code'] ?? '',
        'name' => $c['name'] ?? '',
        'description' => $c['description'] ?? '',
        'section' => $c['section'] ?? 'General',
        'instructor_id' => $c['instructor_id'] ?? null,
        'instructor_name' => $owner['name'] ?? null,
        'present_time' => $c['present_time'] ?? '',
        'late_time' => $c['late_time'] ?? '',
        'end_time' => $c['end_time'] ?? '',
        'created_at' => $c['created_at'] ?? time(),
        'student_count' => count($s['enrollments'][$c['id']] ?? []),
    ];
}

function demo_add_user(array &$s, array $data): array
{
    $id = (int)($s['next_user_id'] ?? 1);
    $s['next_user_id'] = $id + 1;
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $local = strstr($email, '@', true) ?: 'user' . $id;
    $role = in_array($data['role'] ?? 'student', ['student', 'instructor'], true) ? $data['role'] : 'student';
    $u = [
        'id' => $id,
        'name' => (string)($data['name'] ?? $data['full_name'] ?? $local),
        'full_name' => (string)($data['name'] ?? $data['full_name'] ?? $local),
        'username' => (string)($data['username'] ?? $local),
        'email' => $email,
        'role' => $role,
        'school_id' => (string)($data['school_id'] ?? ''),
        'section' => (string)($data['section'] ?? ''),
        'student_id' => $role === 'student' ? 'face' . str_pad((string)$id, 3, '0', STR_PAD_LEFT) : null,
        'has_face' => $role === 'student' && !empty($data['has_face']),
        'is_active' => true,
    ];
    $s['users'][] = $u;
    return $u;
}

function demo_class_body(array $body): array
{
    return [
        'name' => trim((string)($body['name'] ?? '')),
        'description' => trim((string)($body['description'] ?? '')),
        'section' => trim((string)($body['section'] ?? '')) ?: 'General',
        'instructor_id' => isset($body['instructor_id']) && $body['instructor_id'] !== '' ? (int)$body['instructor_id'] : null,
        'present_time' => (string)($body['present_time'] ?? ''),
        'late_time' => (string)($body['late_time'] ?? ''),
        'end_time' => (string)($body['end_time'] ?? ''),
    ];
}

function demo_add_class(array &$s, array $body, array $actor): array
{
    $data = demo_class_body($body);
    if ($data['name'] === '') {
        throw new ApiException('Give the class a name.', 400);
    }
    if (($actor['role'] ?? '') === 'instructor') {
        $data['instructor_id'] = (int)($actor['id'] ?? 0);
    }
    $c = $data + [
        'id' => bin2hex(random_bytes(4)),
        'code' => strtoupper(bin2hex(random_bytes(3))),
        'created_at' => microtime(true),
    ];
    $s['classes'][] = $c;
    $s['enrollments'][$c['id']] = [];
    $s['announcements'][$c['id']] = [];
    $s['attendance'][$c['id']] = [];
    return $c;
}

function demo_visible_classes(array $user): array
{
    $s =& demo_state();
    $out = [];
    foreach ($s['classes'] as $c) {
        $allowed = ($user['role'] ?? '') === 'admin'
            || (($user['role'] ?? '') === 'instructor' && (int)($c['instructor_id'] ?? 0) === (int)($user['id'] ?? 0))
            || (($user['role'] ?? '') === 'student' && demo_enrolled($c['id'], $user['id']));
        if ($allowed) {
            $out[] = demo_class_public($c);
        }
    }
    usort($out, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));
    return $out;
}

function demo_roster($cid): array
{
    $s =& demo_state();
    $out = [];
    foreach ($s['enrollments'][$cid] ?? [] as $uid) {
        $u = demo_user_by_id($uid);
        if (!$u) {
            continue;
        }
        $day = $s['attendance'][$cid][$uid][date('Y-m-d')] ?? null;
        $out[] = [
            'id' => $u['student_id'] ?? (string)$uid,
            'name' => $u['name'] ?? '',
            'status' => $day['status'] ?? 'absent',
            'time' => $day['time'] ?? '',
        ];
    }
    return $out;
}

function demo_session_status(): array
{
    $s =& demo_state();
    $session = null;
    if (!empty($s['session']['active'])) {
        $cid = $s['session']['class_id'];
        $c = demo_find_class($cid);
        $session = [
            'id' => $s['session']['id'],
            'name' => $c['name'] ?? 'Class',
            'class_id' => $cid,
            'started' => $s['session']['started'] ?? date('H:i:s'),
            'student_count' => count($s['enrollments'][$cid] ?? []),
        ];
    }
    return [
        'active' => !empty($s['session']['active']),
        'session' => $session,
        'roster' => demo_roster($s['session']['class_id'] ?? ''),
        'model_ready' => true,
    ];
}

function demo_attendance(string $cid, string $month, string $filter, $studentId): array
{
    $s =& demo_state();
    $u = demo_user_by_id($studentId);
    if (!$u) {
        $uids = $s['enrollments'][$cid] ?? [];
        $uid = $uids[0] ?? null;
        $u = $uid ? demo_user_by_id($uid) : null;
    }
    $days = [];
    if ($u) {
        foreach ($s['attendance'][$cid][$u['id']] ?? [] as $date => $entry) {
            if (str_starts_with($date, $month)) {
                $days[$date] = $entry;
            }
        }
        foreach ($s['excuses'] ?? [] as $x) {
            if (($x['class_id'] ?? '') === $cid
                && (int)($x['user_id'] ?? 0) === (int)($u['id'] ?? 0)
                && str_starts_with((string)($x['date'] ?? ''), $month)) {
                $days[$x['date']] = ['status' => 'excused', 'time' => '', 'note' => $x['note'] ?? ''];
            }
        }
        ksort($days);
    }
    $summary = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];
    foreach ($days as $entry) {
        $status = $entry['status'] ?? 'absent';
        if (isset($summary[$status])) {
            $summary[$status]++;
        }
    }
    $matches = $filter !== '' ? array_filter($days, fn($e) => ($e['status'] ?? '') === $filter) : $days;
    return [
        'month' => $month,
        'days' => $days,
        'summary' => $summary,
        'matches' => $matches,
        'excuses' => array_values(array_filter($s['excuses'] ?? [], fn($x) => ($x['class_id'] ?? '') === $cid)),
        'student' => $u ? demo_public($u) : null,
    ];
}

/** Local replacement for every backend call. Never touches the network. */
function demo_api(string $method, string $path, $body, ?string $token): array
{
    $method = strtoupper($method);
    $parsed = parse_url($path);
    $route = rawurldecode((string)($parsed['path'] ?? '/'));
    $query = [];
    parse_str((string)($parsed['query'] ?? ''), $query);
    $body = is_array($body) ? $body : [];
    $s =& demo_state();
    $user = demo_user_by_token($token);
    $role = (string)($user['role'] ?? '');
    $uid = (int)($user['id'] ?? 0);

    $needUser = function () use ($user) {
        return $user ? null : [401, ['detail' => 'Please log in again.']];
    };
    if ($needUser = $needUser()) {
        // Public auth routes are handled before this point.
        if (!str_starts_with($route, '/auth/login') && !str_starts_with($route, '/auth/register')) {
            return $needUser;
        }
    }

    /* ---------------- auth ---------------- */
    if ($route === '/auth/login' && $method === 'POST') {
        $identifier = strtolower(trim((string)($body['email'] ?? '')));
        if ($identifier === '') {
            return [401, ['detail' => 'Email or password is required']];
        }
        $found = null;
        foreach ($s['users'] as $u) {
            if (strtolower((string)($u['email'] ?? '')) === $identifier
                || strtolower((string)($u['username'] ?? '')) === $identifier) {
                $found = $u;
                break;
            }
        }
        if (!$found) {
            $demoRole = stripos($identifier, 'admin') !== false ? 'admin'
                : (stripos($identifier, 'instructor') !== false || stripos($identifier, 'teacher') !== false || stripos($identifier, 'prof') !== false
                    ? 'instructor' : 'student');
            $local = strstr($identifier, '@', true) ?: $identifier;
            $found = demo_add_user($s, [
                'name' => ucwords(str_replace(['.', '_', '-'], ' ', $local)),
                'email' => str_contains($identifier, '@') ? $identifier : $identifier . '@demo.local',
                'username' => $local,
                'role' => $demoRole,
                'has_face' => $demoRole === 'student',
            ]);
        }
        return [200, [
            'user' => demo_public($found),
            'access_token' => DEMO_TOKEN_PREFIX . $found['id'],
            'token_type' => 'bearer',
        ]];
    }

    if ($route === '/auth/register' && $method === 'POST') {
        $email = strtolower(trim((string)($body['email'] ?? '')));
        foreach ($s['users'] as $u) {
            if (strtolower((string)($u['email'] ?? '')) === $email) {
                return [409, ['detail' => 'That email is already registered.']];
            }
        }
        $new = demo_add_user($s, $body + ['has_face' => ($body['role'] ?? 'student') === 'student']);
        return [201, [
            'user' => demo_public($new),
            'access_token' => DEMO_TOKEN_PREFIX . $new['id'],
            'token_type' => 'bearer',
        ]];
    }

    if ($route === '/auth/me' && $method === 'GET') {
        return [200, demo_public($user)];
    }
    if ($route === '/auth/change-password' && $method === 'POST') {
        return [200, ['message' => 'Password changed']];
    }
    if ($route === '/auth/profile' && $method === 'POST') {
        foreach ($s['users'] as &$u) {
            if ((int)($u['id'] ?? 0) === $uid) {
                $u['name'] = (string)($body['name'] ?? $u['name'] ?? '');
                $u['full_name'] = $u['name'];
                $u['school_id'] = (string)($body['school_id'] ?? $u['school_id'] ?? '');
                $u['section'] = (string)($body['section'] ?? $u['section'] ?? '');
                return [200, ['user' => demo_public($u)]];
            }
        }
        unset($u);
        return [404, ['detail' => 'User not found']];
    }
    if ($route === '/auth/photos' && $method === 'POST') {
        foreach ($s['users'] as &$u) {
            if ((int)($u['id'] ?? 0) === $uid) {
                $u['has_face'] = true;
                if (empty($u['student_id'])) {
                    $u['student_id'] = 'face' . str_pad((string)$uid, 3, '0', STR_PAD_LEFT);
                }
                return [200, ['ok' => true, 'message' => 'Your 3 photos were saved.', 'student_id' => $u['student_id']]];
            }
        }
        unset($u);
        return [200, ['ok' => true]];
    }
    if ($route === '/auth/avatar' && $method === 'POST') {
        return [200, ['ok' => true]];
    }

    /* ---------------- admin ---------------- */
    if (str_starts_with($route, '/admin/') && $role !== 'admin') {
        return [403, ['detail' => 'You are not allowed to do this']];
    }
    if ($route === '/admin/users' && $method === 'GET') {
        $filter = (string)($query['role'] ?? '');
        $list = [];
        foreach ($s['users'] as $u) {
            $ur = (string)($u['role'] ?? '');
            if ($filter !== '' ? $ur === $filter : $ur !== 'admin') {
                $list[] = demo_public($u);
            }
        }
        usort($list, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return [200, $list];
    }
    if ($route === '/admin/users' && $method === 'POST') {
        $new = demo_add_user($s, $body);
        return [201, demo_public($new)];
    }
    if ($route === '/admin/instructors' && $method === 'POST') {
        $new = demo_add_user($s, $body + ['role' => 'instructor']);
        return [201, demo_public($new)];
    }
    if (preg_match('#^/admin/users/(\d+)$#', $route, $m)) {
        $target = (int)$m[1];
        $targetUser = demo_user_by_id($target);
        if (!$targetUser || ($targetUser['role'] ?? '') === 'admin') {
            return [404, ['detail' => 'User not found']];
        }
        if ($method === 'PUT') {
            foreach ($s['users'] as &$u) {
                if ((int)($u['id'] ?? 0) === $target) {
                    foreach (['name', 'email', 'username', 'school_id', 'section', 'role'] as $key) {
                        if (array_key_exists($key, $body)) {
                            $u[$key] = $body[$key];
                        }
                    }
                    $u['full_name'] = $u['name'];
                    return [200, demo_public($u)];
                }
            }
            unset($u);
        }
        if ($method === 'DELETE') {
            $s['users'] = array_values(array_filter($s['users'], fn($u) => (int)($u['id'] ?? 0) !== $target));
            foreach ($s['enrollments'] as &$ids) {
                $ids = array_values(array_filter($ids, fn($id) => (int)$id !== $target));
            }
            unset($ids);
            return [200, ['ok' => true]];
        }
    }
    if ($route === '/admin/classes' && $method === 'GET') {
        $list = array_map('demo_class_public', $s['classes']);
        usort($list, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));
        return [200, $list];
    }
    if ($route === '/admin/classes' && $method === 'POST') {
        return [201, demo_class_public(demo_add_class($s, $body, $user))];
    }
    if (preg_match('#^/admin/classes/([^/]+)$#', $route, $m)) {
        $c = demo_find_class($m[1]);
        if (!$c) {
            return [404, ['detail' => 'No such class.']];
        }
        if ($method === 'PUT') {
            foreach ($s['classes'] as &$item) {
                if (($item['id'] ?? '') === $m[1]) {
                    $data = demo_class_body($body);
                    foreach ($data as $key => $value) {
                        $item[$key] = $value;
                    }
                    return [200, demo_class_public($item)];
                }
            }
            unset($item);
        }
        if ($method === 'DELETE') {
            $s['classes'] = array_values(array_filter($s['classes'], fn($x) => ($x['id'] ?? '') !== $m[1]));
            unset($s['enrollments'][$m[1]], $s['announcements'][$m[1]], $s['attendance'][$m[1]]);
            return [200, ['ok' => true]];
        }
    }

    /* ---------------- classes ---------------- */
    if ($route === '/classes' && $method === 'GET') {
        return [200, demo_visible_classes($user)];
    }
    if ($route === '/classes' && $method === 'POST') {
        if (!in_array($role, ['admin', 'instructor'], true)) {
            return [403, ['detail' => 'Only instructors can create classes.']];
        }
        return [201, demo_class_public(demo_add_class($s, $body, $user))];
    }
    if ($route === '/classes/join' && $method === 'POST') {
        if ($role !== 'student') {
            return [403, ['detail' => 'Only students join classes.']];
        }
        $code = strtoupper(trim((string)($body['code'] ?? '')));
        foreach ($s['classes'] as $c) {
            if (strtoupper((string)($c['code'] ?? '')) === $code) {
                if (!demo_enrolled($c['id'], $uid)) {
                    $s['enrollments'][$c['id']][] = $uid;
                }
                return [200, ['ok' => true, 'class_id' => $c['id'], 'name' => $c['name'] ?? '']];
            }
        }
        return [404, ['detail' => 'No class has that code.']];
    }
    if (preg_match('#^/classes/([^/]+)/leave$#', $route, $m) && $method === 'POST') {
        $s['enrollments'][$m[1]] = array_values(array_filter($s['enrollments'][$m[1]] ?? [], fn($id) => (int)$id !== $uid));
        $s['excuses'] = array_values(array_filter($s['excuses'] ?? [], fn($x) => (($x['class_id'] ?? '') !== $m[1] || (int)($x['user_id'] ?? 0) !== $uid)));
        return [200, ['ok' => true]];
    }
    if (preg_match('#^/classes/([^/]+)/students/(\d+)$#', $route, $m) && $method === 'DELETE') {
        $c = demo_find_class($m[1]);
        if (!$c || !demo_manages($user, $c)) {
            return [403, ['detail' => "You don't manage this class."]];
        }
        $s['enrollments'][$m[1]] = array_values(array_filter($s['enrollments'][$m[1]] ?? [], fn($id) => (int)$id !== (int)$m[2]));
        return [200, ['ok' => true]];
    }
    if (preg_match('#^/classes/([^/]+)/students$#', $route, $m) && $method === 'GET') {
        $c = demo_find_class($m[1]);
        if (!$c || !demo_manages($user, $c)) {
            return [403, ['detail' => "You don't manage this class."]];
        }
        $list = [];
        foreach ($s['enrollments'][$m[1]] ?? [] as $id) {
            $u = demo_user_by_id($id);
            if ($u) {
                $list[] = demo_public($u);
            }
        }
        usort($list, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return [200, $list];
    }
    if (preg_match('#^/classes/([^/]+)/announcements$#', $route, $m)) {
        $c = demo_find_class($m[1]);
        if (!$c) {
            return [403, ['detail' => "You don't have access to this class."]];
        }
        $canView = demo_manages($user, $c) || demo_enrolled($m[1], $uid);
        if (!$canView) {
            return [403, ['detail' => "You don't have access to this class."]];
        }
        if ($method === 'GET') {
            return [200, array_values($s['announcements'][$m[1]] ?? [])];
        }
        if ($method === 'POST') {
            if (!demo_manages($user, $c)) {
                return [403, ['detail' => "You don't manage this class."]];
            }
            $title = trim((string)($body['title'] ?? ''));
            $text = trim((string)($body['body'] ?? ''));
            if ($title === '' || $text === '') {
                return [400, ['detail' => 'Add a title and a message.']];
            }
            $id = (int)($s['next_announcement_id'] ?? 1);
            $s['next_announcement_id'] = $id + 1;
            $s['announcements'][$m[1]][] = [
                'id' => $id, 'title' => $title, 'body' => $text,
                'author' => $user['name'] ?? '', 'date' => date('M d, Y H:i'),
            ];
            return [201, ['ok' => true]];
        }
    }
    if (preg_match('#^/classes/([^/]+)/announcements/(\d+)$#', $route, $m) && $method === 'DELETE') {
        $c = demo_find_class($m[1]);
        if (!$c || !demo_manages($user, $c)) {
            return [403, ['detail' => "You don't manage this class."]];
        }
        $s['announcements'][$m[1]] = array_values(array_filter($s['announcements'][$m[1]] ?? [], fn($a) => (int)($a['id'] ?? 0) !== (int)$m[2]));
        return [200, ['ok' => true]];
    }
    if (preg_match('#^/classes/([^/]+)$#', $route, $m)) {
        $c = demo_find_class($m[1]);
        $canView = $c && (demo_manages($user, $c) || demo_enrolled($m[1], $uid));
        if (!$canView) {
            return [403, ['detail' => "You don't have access to this class."]];
        }
        if ($method === 'GET') {
            return [200, demo_class_public($c)];
        }
        if (!demo_manages($user, $c)) {
            return [403, ['detail' => "You don't manage this class."]];
        }
        if ($method === 'PUT') {
            foreach ($s['classes'] as &$item) {
                if (($item['id'] ?? '') === $m[1]) {
                    foreach (demo_class_body($body) as $key => $value) {
                        $item[$key] = $value;
                    }
                    return [200, demo_class_public($item)];
                }
            }
            unset($item);
        }
        if ($method === 'DELETE') {
            $s['classes'] = array_values(array_filter($s['classes'], fn($x) => ($x['id'] ?? '') !== $m[1]));
            unset($s['enrollments'][$m[1]], $s['announcements'][$m[1]], $s['attendance'][$m[1]]);
            return [200, ['ok' => true]];
        }
    }

    /* ---------------- attendance ---------------- */
    if ($route === '/attendance/session' && $method === 'GET') {
        return [200, demo_session_status()];
    }
    if ($route === '/attendance/session/start' && $method === 'POST') {
        if (!in_array($role, ['admin', 'instructor'], true)) {
            return [403, ['detail' => 'You are not allowed to do this']];
        }
        if (!empty($s['session']['active'])) {
            return [409, ['detail' => 'A class is already running.']];
        }
        $cid = (string)($body['class_id'] ?? '');
        $c = demo_find_class($cid);
        if (!$c || !demo_manages($user, $c)) {
            return [403, ['detail' => "You don't manage this class."]];
        }
        $s['session'] = [
            'active' => true, 'class_id' => $cid, 'started' => date('H:i:s'),
            'id' => (int)($s['next_session_id'] ?? 1),
        ];
        $s['next_session_id'] = $s['session']['id'] + 1;
        return [200, [
            'ok' => true,
            'session' => [
                'id' => $s['session']['id'], 'name' => $c['name'] ?? '', 'class_id' => $cid,
                'started_at' => microtime(true), 'marked' => 0, 'owner_id' => $uid,
            ],
        ]];
    }
    if ($route === '/attendance/session/end' && $method === 'POST') {
        if (!in_array($role, ['admin', 'instructor'], true)) {
            return [403, ['detail' => 'You are not allowed to do this']];
        }
        if (!empty($s['session']['active'])) {
            $cid = $s['session']['class_id'];
            $today = date('Y-m-d');
            foreach ($s['enrollments'][$cid] ?? [] as $studentId) {
                if (!isset($s['attendance'][$cid][$studentId][$today])) {
                    $s['attendance'][$cid][$studentId][$today] = ['status' => 'absent', 'time' => ''];
                }
            }
            $s['session'] = ['active' => false, 'class_id' => null, 'started' => '', 'id' => 0];
        }
        return [200, ['ok' => true, 'session' => null]];
    }
    if ($route === '/attendance/frame/reset' && $method === 'POST') {
        return [200, ['ok' => true]];
    }
    if ($route === '/attendance/frame' && $method === 'POST') {
        if (!in_array($role, ['admin', 'instructor'], true)) {
            return [403, ['detail' => 'You are not allowed to do this']];
        }
        if (empty($s['session']['active'])) {
            return [409, ['detail' => 'Start a class before scanning.']];
        }
        $cid = $s['session']['class_id'];
        $c = demo_find_class($cid) ?: [];
        $late = (string)($c['late_time'] ?? '08:15');
        $state = date('H:i') >= $late ? 'late' : 'present';
        $faces = [];
        $i = 0;
        foreach ($s['enrollments'][$cid] ?? [] as $studentId) {
            if ($i >= 3) {
                break;
            }
            $u = demo_user_by_id($studentId);
            if (!$u) {
                continue;
            }
            $statuses = ['present', 'blink', 'checking'];
            $faces[] = [
                'name' => $u['name'] ?? '',
                'state' => $statuses[$i % 3],
                'box' => [0.12 + $i * 0.27, 0.18, 0.34 + $i * 0.27, 0.62],
            ];
            if ($statuses[$i % 3] === 'present') {
                $s['attendance'][$cid][$studentId][date('Y-m-d')] = [
                    'status' => $state, 'time' => date('H:i:s'),
                ];
            }
            $i++;
        }
        return [200, [
            'faces' => $faces,
            'roster' => demo_roster($cid),
            'session' => demo_session_status()['session'],
            'model_ready' => true,
        ]];
    }
    if (preg_match('#^/attendance/class/([^/]+)$#', $route, $m) && $method === 'GET') {
        $c = demo_find_class($m[1]);
        $canView = $c && (demo_manages($user, $c) || demo_enrolled($m[1], $uid));
        if (!$canView) {
            return [403, ['detail' => "You don't have access to this class."]];
        }
        $month = (string)($query['month'] ?? date('Y-m'));
        $filter = (string)($query['status'] ?? '');
        $studentId = $role === 'student' ? $uid : (int)($query['student_id'] ?? 0);
        return [200, demo_attendance($m[1], $month, $filter, $studentId)];
    }
    if (preg_match('#^/attendance/class/([^/]+)/sessions$#', $route, $m) && $method === 'GET') {
        return [200, [['id' => 1, 'name' => 'Demo session', 'date' => date('M d, Y H:i'), 'present' => 2, 'ended' => true]]];
    }
    if ($route === '/attendance/excuse' && $method === 'POST') {
        if ($role !== 'student') {
            return [403, ["detail" => "You're not enrolled in this class."]];
        }
        $date = (string)($body['date'] ?? '');
        $note = trim((string)($body['note'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return [400, ['detail' => 'Pick a valid date.']];
        }
        if ($note === '') {
            return [400, ['detail' => 'Write a short note for your excuse.']];
        }
        $cid = (string)($body['class_id'] ?? '');
        foreach ($s['excuses'] as &$x) {
            if (($x['class_id'] ?? '') === $cid && (int)($x['user_id'] ?? 0) === $uid && ($x['date'] ?? '') === $date) {
                $x['note'] = $note;
                return [200, ['ok' => true, 'date' => $date, 'note' => $note]];
            }
        }
        unset($x);
        $s['excuses'][] = ['class_id' => $cid, 'user_id' => $uid, 'date' => $date, 'note' => $note, 'name' => $user['name'] ?? ''];
        return [200, ['ok' => true, 'date' => $date, 'note' => $note]];
    }
    if (preg_match('#^/attendance/excuse/([^/]+)/([^/]+)$#', $route, $m) && $method === 'DELETE') {
        if ($role !== 'student') {
            return [403, ['detail' => "You're not enrolled in this class."]];
        }
        $s['excuses'] = array_values(array_filter($s['excuses'] ?? [], fn($x) => (($x['class_id'] ?? '') !== $m[1]
            || (int)($x['user_id'] ?? 0) !== $uid || ($x['date'] ?? '') !== $m[2])));
        return [200, ['ok' => true]];
    }
    if (preg_match('#^/attendance/excuses/([^/]+)$#', $route, $m) && $method === 'GET') {
        $c = demo_find_class($m[1]);
        if (!$c || !(demo_manages($user, $c) || demo_enrolled($m[1], $uid))) {
            return [403, ['detail' => "You don't have access to this class."]];
        }
        $list = array_values(array_filter($s['excuses'] ?? [], fn($x) => ($x['class_id'] ?? '') === $m[1]));
        usort($list, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
        return [200, $list];
    }

    /* ---------------- notifications ---------------- */
    if ($route === '/notifications' && $method === 'GET') {
        $items = array_values(array_filter($s['notifications'] ?? [], fn($n) => (int)($n['user_id'] ?? 0) === $uid));
        $items = array_slice(array_reverse($items), 0, 40);
        $unread = count(array_filter($items, fn($n) => empty($n['read'])));
        return [200, ['unread' => $unread, 'items' => $items]];
    }
    if ($route === '/notifications/read' && $method === 'POST') {
        foreach ($s['notifications'] as &$n) {
            if (!empty($body['all']) || (int)($body['id'] ?? 0) === (int)($n['id'] ?? 0)) {
                $n['read'] = true;
            }
        }
        unset($n);
        return [200, ['ok' => true]];
    }

    return [404, ['detail' => 'Not Found']];
}

/** Kept for compatibility: all calls are answered locally in demo mode. */
function api_call(string $method, string $path, $body = null, ?string $token = null): array
{
    return demo_api($method, $path, $body, $token);
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

function api(string $method, string $path, $body = null, ?string $token = null)
{
    [$status, $data] = api_call($method, $path, $body, $token ?? ($_SESSION['token'] ?? null));
    if ($status >= 200 && $status < 300) {
        return $data;
    }
    throw new ApiException(api_error_message($data, $status), $status);
}

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
