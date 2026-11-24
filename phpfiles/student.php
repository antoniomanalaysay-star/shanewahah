<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('student');
$joining = query('join') === '1';
$error = '';
$code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = input('code');
    [$ok, $message, $data] = perform_action(function () use ($code) {
        if ($code === '') {
            throw new ApiException('Enter the class code.');
        }
        return api('POST', '/classes/join', ['code' => $code]);
    });
    if ($ok) {
        $name = $data['name'] ?? 'the class';
        flash('Joined ' . $name . '!');
        redirect('student.php');
    }
    $error = $message;
    $joining = true;
}

$classes = safe_api('GET', '/classes') ?: [];
$cards = '';
foreach ($classes as $c) {
    $cards .= class_card($c, 'class.php?id=' . rawurlencode((string)($c['id'] ?? '')));
}

if (!empty($user['has_face'])) {
    $faceCard = info_card(
        '<div class="list-row"><span class="circle-avatar" style="color:#4CAF50">' . icon('verified', 24, '#4CAF50') . '</span>' .
        '<div class="row-text"><strong>Your face is registered</strong></div>' .
        text_button('Update photos', 'face_registration.php') . '</div>'
    );
} else {
    $faceCard = section_card('Register your face',
        '<p class="muted">Add 3 clear photos (front, left and right) so the instructor’s phone can recognize you during class.</p>' .
        button('Add my 3 photos', ['icon' => 'face', 'href' => 'face_registration.php'])
    );
}

$joinForm = '<form method="post" action="student.php">' .
    field('code', 'Class code', ['icon' => 'qr', 'value' => $code, 'required' => true]) .
    '<div class="gap16"></div>' . button('Join class', ['type' => 'submit']) . '</form>';

$content = page_title('Student workspace', 'Join a class with its code, read announcements and track your attendance.') .
    $faceCard . '<div class="gap18"></div>' .
    section_card('My classes', error_banner($error ?: null) .
        ($joining ? $joinForm : '') .
        ($cards ? '<div class="gap16"></div><div class="class-grid">' . $cards . '</div>' : '<div class="gap16"></div><div class="empty-state">No classes yet. Join one with a class code.</div>'),
        $joining ? text_button('Close', 'student.php') : text_button('Join a class', 'student.php?join=1', ['icon' => 'add'])
    );
shell_page($user, 'classes', $content);
