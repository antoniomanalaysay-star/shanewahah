<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

if (current_user()) {
    redirect('index.php');
}
if (query('logged_out') === '1') {
    flash('Logged out.');
}

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = input('email');
    $password = (string)($_POST['password'] ?? '');
    [$ok, $message, $data] = perform_action(function () use ($email, $password) {
        if ($email === '' || $password === '') {
            throw new ApiException('Email or password is required.');
        }
        return api('POST', '/auth/login', ['email' => $email, 'password' => $password], null);
    });
    if ($ok) {
        login_user($data ?: []);
        flash('Welcome back!');
        redirect('index.php');
    }
    $error = $message;
}

$form = '<h1>Welcome back</h1>' .
    '<p class="auth-sub">Log in with the username or email and password you created.</p>' .
    error_banner($error ?: null) .
    '<form method="post" action="login.php" autocomplete="on">' .
    field('email', 'Username or email', ['icon' => 'person', 'value' => $email, 'required' => true, 'extra' => 'autocomplete="username"']) .
    field('password', 'Password', ['icon' => 'lock', 'required' => true, 'extra' => 'autocomplete="current-password"']) .
    '<a class="text-btn tone-gold forgot" href="#">Forgot password?</a>' .
    '<div class="gap18"></div>' .
    button('Log in', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
    '</form>' .
    '<div class="gap18"></div>' .
    '<p class="auth-sub" style="text-align:center">No account yet? <a class="text-btn tone-lime" href="register.php">Create one</a></p>';

auth_layout(
    "A Camera-Based Classroom\nAttendance & Management System",
    'Recognize students at the door, keep a clean attendance calendar for every class, and give teachers and students their own workspace.',
    [
        'Face registration with 3 photos',
        'Live recognition with blink check',
        'Color-coded monthly attendance',
        'Announcements, excuses & class codes',
    ],
    'Admin demo: ThirdyAdmin / Admin123',
    $form
);
