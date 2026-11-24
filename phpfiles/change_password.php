<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_user();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = (string)($_POST['old_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    [$ok, $message] = perform_action(function () use ($old, $new, $confirm) {
        if ($old === '') {
            throw new ApiException('Current password is required');
        }
        if (strlen($new) < 8) {
            throw new ApiException('At least 8 characters');
        }
        if ($new !== $confirm) {
            throw new ApiException('Passwords do not match');
        }
        return api('POST', '/auth/change-password', ['old_password' => $old, 'new_password' => $new]);
    });
    if ($ok) {
        flash('Password changed');
        redirect('profile.php');
    }
    $error = $message;
}

$content = '<div class="center-form">' .
    page_title('Change password', 'Use at least 8 characters for your new password.') .
    info_card(
        error_banner($error ?: null) .
        '<form method="post" action="change_password.php">' .
        field('old_password', 'Current password', ['type' => 'password', 'icon' => 'lock', 'required' => true]) .
        field('new_password', 'New password', ['type' => 'password', 'icon' => 'lock_reset', 'required' => true]) .
        field('confirm_password', 'Confirm new password', ['type' => 'password', 'icon' => 'lock_reset', 'required' => true]) .
        button('Save new password', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
        '</form>'
    ) . '</div>';
shell_page($user, 'profile', $content);
