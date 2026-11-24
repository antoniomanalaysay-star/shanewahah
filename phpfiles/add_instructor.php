<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('admin');
$error = '';
$old = ['name' => '', 'school_id' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = ['name' => input('name'), 'school_id' => input('school_id'), 'email' => input('email')];
    $password = (string)($_POST['password'] ?? '');
    [$ok, $message] = perform_action(function () use ($old, $password) {
        if ($old['name'] === '') {
            throw new ApiException('Full name is required');
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ApiException('Enter a valid email');
        }
        if (strlen($password) < 8) {
            throw new ApiException('At least 8 characters');
        }
        return api('POST', '/admin/instructors', [
            'name' => $old['name'],
            'school_id' => $old['school_id'],
            'email' => $old['email'],
            'password' => $password,
        ]);
    });
    if ($ok) {
        flash('Instructor account created for ' . $old['name']);
        redirect('admin.php');
    }
    $error = $message;
}

$content = '<div class="center-form">' .
    page_title('Add instructor', 'Create an instructor account for your school.') .
    info_card(error_banner($error ?: null) .
        '<form method="post" action="add_instructor.php">' .
        field('name', 'Full name', ['icon' => 'person', 'value' => $old['name'], 'required' => true]) .
        field('school_id', 'School ID (optional)', ['icon' => 'badge', 'value' => $old['school_id']]) .
        field('email', 'Email', ['type' => 'email', 'icon' => 'mail', 'value' => $old['email'], 'required' => true]) .
        field('password', 'First password', ['type' => 'password', 'icon' => 'lock', 'required' => true]) .
        '<p class="muted small">Give this password to the instructor. They can change it from the account menu after logging in.</p>' .
        button('Create instructor account', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
        '</form>') . '</div>';
shell_page($user, 'people', $content);
