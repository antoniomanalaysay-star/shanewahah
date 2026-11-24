<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_user();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    [$ok, $message] = perform_action(function () use ($action, $user) {
        if ($action === 'avatar') {
            $image = data_uri_from_upload($_FILES['avatar'] ?? []);
            if ($image === null) {
                throw new ApiException('That image could not be read.');
            }
            api('POST', '/auth/avatar', ['image' => $image]);
            return true;
        }
        $name = input('name');
        if ($name === '') {
            throw new ApiException('Full name is required');
        }
        api('POST', '/auth/profile', [
            'name' => $name,
            'school_id' => (string)($user['school_id'] ?? ''),
            'section' => (string)($user['section'] ?? ''),
        ]);
        $_SESSION['user'] = api('GET', '/auth/me');
        return true;
    });
    if ($ok) {
        flash($action === 'avatar' ? 'Picture updated.' : 'Name saved.');
        redirect('profile.php');
    }
    $error = $message;
}

$initial = function_exists('mb_substr') ? mb_substr((string)($user['name'] ?? ''), 0, 1) : substr((string)($user['name'] ?? ''), 0, 1);
$avatar = '<img id="avatarPreview" class="circle-avatar" src="' . h(avatar_url($user)) . '" alt="" data-initial="' . h(strtoupper((string)$initial)) . '">';

$identity = info_card(
    '<div class="list-row"><span class="circle-avatar">' . h(strtoupper((string)$initial)) . '</span>' .
    '<div class="row-text"><strong>' . h($user['name'] ?? '') . '</strong>' .
    '<small>' . h($user['role'] ?? '') . ' · @' . h($user['username'] ?? '') . '</small></div></div>' .
    '<div class="gap12"></div><p class="muted">' . h($user['email'] ?? '') . '</p>'
);

$picture = info_card(
    '<h2>Profile picture</h2><p class="muted small" style="margin-top:6px">A square image works best.</p>' .
    error_banner($error ?: null) .
    '<form method="post" action="profile.php" enctype="multipart/form-data" style="margin-top:14px">' .
    '<div style="display:flex;align-items:center;gap:14px">' . $avatar .
    '<input class="photo-input" type="file" name="avatar" accept="image/*" data-preview="avatarPreview" data-auto-submit hidden>' .
    button('Choose picture', ['variant' => 'tonal', 'icon' => 'upload', 'name' => 'action', 'value' => 'avatar', 'type' => 'submit', 'onclick' => "document.querySelector('[name=avatar]').click();return false"]) .
    '</div></form>'
);

$details = info_card(
    '<h2>Account details</h2><div class="gap12"></div>' .
    '<form method="post" action="profile.php" style="display:flex;flex-direction:column;gap:12px">' .
    field('name', 'Full name', ['icon' => 'person', 'value' => (string)($user['name'] ?? ''), 'required' => true]) .
    field('username', 'Username', ['icon' => 'at', 'value' => (string)($user['username'] ?? ''), 'extra' => 'readonly']) .
    field('email', 'Email', ['icon' => 'mail', 'value' => (string)($user['email'] ?? ''), 'extra' => 'readonly']) .
    '<input type="hidden" name="action" value="name">' .
    button('Save name', ['type' => 'submit']) . '</form>'
);

$password = info_card(
    '<h2>Password</h2><p class="muted small" style="margin-top:6px">Change your password regularly.</p>' .
    '<div class="gap16"></div>' . button('Change password', ['variant' => 'tonal', 'icon' => 'lock_reset', 'href' => 'change_password.php'])
);

$face = '';
if (($user['role'] ?? '') === 'student') {
    $face = info_card(
        '<h2>My face registration</h2><div class="gap12"></div>' .
        '<p class="muted">' . (!empty($user['has_face'])
            ? 'Your 3 photos are registered.'
            : 'Add 3 photos so the camera can recognize you.') . '</p>' .
        '<div class="gap12"></div>' . button(!empty($user['has_face']) ? 'Update photos' : 'Add photos', [
            'variant' => 'tonal',
            'icon' => 'face',
            'href' => 'face_registration.php',
        ])
    );
}

$content = page_title('My profile', 'Manage your account details, picture and password.') .
    '<div class="profile-grid">' . $identity . $picture . $details . $password . $face . '</div>';
shell_page($user, 'profile', $content);
