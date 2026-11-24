<?php
/**
 * Root router: sends each role to the same dashboard the Flutter app opens.
 */
require_once __DIR__ . '/core.php';

if (query('action') === 'logout') {
    logout_user();
    redirect('login.php?logged_out=1');
}

$user = current_user(true);
if (!$user) {
    redirect('login.php');
}
$page = match ($user['role'] ?? '') {
    'admin' => 'admin.php',
    'instructor' => 'instructor.php',
    'student' => 'student.php',
    default => 'login.php',
};
redirect($page);
