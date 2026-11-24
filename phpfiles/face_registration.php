<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_user();
if (($user['role'] ?? '') !== 'student') {
    redirect('index.php');
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $message] = perform_action(function () {
        $photos = [];
        for ($i = 1; $i <= 3; $i++) {
            $photos[] = data_uri_from_upload($_FILES['photo' . $i] ?? []);
        }
        if (in_array(null, $photos, true)) {
            throw new ApiException('Choose exactly 3 photos.');
        }
        return api('POST', '/auth/photos', ['photos' => $photos]);
    });
    if ($ok) {
        $_SESSION['user'] = safe_api('GET', '/auth/me') ?: $user;
        flash('Face registration saved');
        redirect('profile.php');
    }
    $error = $message;
}

$rows = '';
for ($i = 1; $i <= 3; $i++) {
    $rows .= '<label class="list-row face-pick-row">' .
        '<span class="circle-avatar">' . icon('add_photo', 20, '#8FAE99') . '</span>' .
        '<span class="row-text"><strong>Photo ' . $i . '<span class="selected-label"></span></strong>' .
        '<small>Tap to choose from your gallery</small></span>' .
        '<img class="face-pick-preview" id="facePreview' . $i . '" alt="Photo ' . $i . '" hidden>' .
        '<input class="photo-input" type="file" name="photo' . $i . '" accept="image/*" data-preview="facePreview' . $i . '" hidden>' .
        '</label>';
}

$content = '<div class="center-form wide">' .
    page_title('Face verification', 'Your photos are used only to recognize you in class.') .
    info_card(
        '<h2>Three clear photos of your face</h2>' .
        '<p class="muted small" style="margin-top:8px">Face the camera, keep good lighting and avoid hats or sunglasses. The instructor\'s phone will recognise you during class.</p>' .
        (!empty($user['has_face']) ? '<div class="gap12"></div><p class="tone-lime small">' . icon('info', 16) . ' You already have a registered face. Uploading again replaces it.</p>' : '')
    ) .
    '<div class="gap16"></div>' .
    info_card(error_banner($error ?: null) . '<form method="post" action="face_registration.php" enctype="multipart/form-data">' .
        $rows .
        '<div class="gap16"></div>' .
        button('Save my photos', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
        '</form>') . '</div>';
shell_page($user, 'profile', $content);
