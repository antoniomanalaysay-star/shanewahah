<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

if (current_user()) {
    redirect('index.php');
}

$error = '';
$old = ['name' => '', 'username' => '', 'email' => '', 'role' => 'student'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name' => input('name'),
        'username' => input('username'),
        'email' => input('email'),
        'role' => input('role', 'student') === 'instructor' ? 'instructor' : 'student',
    ];
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    [$ok, $message, $data] = perform_action(function () use ($old, $password, $confirm) {
        if ($old['name'] === '') {
            throw new ApiException('Full name is required');
        }
        if ($old['username'] === '') {
            throw new ApiException('Username is required');
        }
        if (preg_match('/\s/', $old['username'])) {
            throw new ApiException('Username cannot contain spaces');
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ApiException('Enter a valid email');
        }
        if (strlen($password) < 8) {
            throw new ApiException('At least 8 characters');
        }
        if ($password !== $confirm) {
            throw new ApiException('Passwords do not match');
        }
        return api('POST', '/auth/register', [
            'name' => $old['name'],
            'username' => $old['username'],
            'email' => $old['email'],
            'password' => $password,
            'role' => $old['role'],
        ], null);
    });

    if ($ok) {
        login_user($data ?: []);
        $photoError = '';
        if ($old['role'] === 'student') {
            // Demo mode: photos are optional. The UI still shows the 3 slots.
            $photos = [];
            for ($i = 1; $i <= 3; $i++) {
                $photos[] = data_uri_from_upload($_FILES['photo' . $i] ?? []) ?: 'data:image/jpeg;base64,demo';
            }
            [$pOk, $pMessage] = perform_action(function () use ($photos) {
                return api('POST', '/auth/photos', ['photos' => $photos]);
            });
            if (!$pOk) {
                $photoError = $pMessage;
            }
        }
        $_SESSION['user'] = safe_api('GET', '/auth/me') ?: ($_SESSION['user'] ?? null);
        flash($photoError ?: 'Account created. Welcome to AtClam!', (bool)$photoError);
        redirect('index.php');
    }
    $error = $message;
}

$roleStudent = $old['role'] === 'student' ? ' selected' : '';
$roleInstructor = $old['role'] === 'instructor' ? ' selected' : '';
$slots = '';
foreach ([1 => 'Front', 2 => 'Left', 3 => 'Right'] as $n => $label) {
    $slots .= '<label class="face-slot"><input class="photo-input" type="file" accept="image/*" name="photo' . $n . '" data-preview="preview' . $n . '" hidden>' .
        icon('add_photo', 24, '#8FAE99') . '<img id="preview' . $n . '" alt="' . $label . '" hidden>' .
        '<span>' . $label . '</span></label>';
}

$form = '<h1>Create your account</h1>' .
    '<p class="auth-sub">Tell us who you are. You’ll go straight to your workspace.</p>' .
    error_banner($error ?: null) .
    '<form method="post" action="register.php" enctype="multipart/form-data">' .
    '<div class="role-grid">' .
    '<label class="role-card' . $roleStudent . '" data-role="student">' .
    '<span class="role-row">' . icon('school', 20, '#F5C518') . 'Student</span>' .
    '<small>View my attendance</small></label>' .
    '<label class="role-card' . $roleInstructor . '" data-role="instructor">' .
    '<span class="role-row">' . icon('person', 20, '#F5C518') . 'Instructor</span>' .
    '<small>Create &amp; manage classes</small></label></div>' .
    '<input type="hidden" name="role" id="roleInput" value="' . h($old['role']) . '">' .
    field('name', 'Full name', ['icon' => 'person', 'value' => $old['name'], 'required' => true, 'extra' => 'autocomplete="name"']) .
    field('username', 'Username', ['icon' => 'at', 'value' => $old['username'], 'required' => true]) .
    field('email', 'Email', ['type' => 'email', 'icon' => 'mail', 'value' => $old['email'], 'required' => true, 'extra' => 'autocomplete="email"']) .
    field('password', 'Password', ['type' => 'password', 'icon' => 'lock', 'required' => true, 'extra' => 'autocomplete="new-password"']) .
    field('confirm_password', 'Confirm password', ['type' => 'password', 'icon' => 'lock', 'required' => true, 'extra' => 'autocomplete="new-password"']) .
    '<div id="studentPhotos"' . ($old['role'] === 'student' ? '' : ' hidden') . '>' .
    '<div class="gap18"></div><strong>Registration photos (3 required)</strong>' .
    '<p class="muted small" style="margin-top:4px">Front, left and right views let the camera recognize you.</p>' .
    '<div class="face-slots">' . $slots . '</div></div>' .
    '<div id="instructorNote"' . ($old['role'] === 'instructor' ? '' : ' hidden') . '>' .
    '<div class="gap18"></div><p class="muted small">' . icon('info', 16, '#A8E063') .
    ' Instructors don’t need face photos. You can create classes and take attendance right away.</p></div>' .
    '<div class="gap20"></div>' .
    button('Sign up & continue', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
    '</form>' .
    '<div class="gap18"></div>' .
    '<p class="auth-sub" style="text-align:center">Already have an account? <a class="text-btn tone-lime" href="login.php">Log in</a></p>' .
    '<script>' .
    '(function(){var cards=document.querySelectorAll(".role-card"),input=document.getElementById("roleInput"),' .
    'photos=document.getElementById("studentPhotos"),note=document.getElementById("instructorNote");' .
    'function select(role){input.value=role;photos.hidden=role!=="student";note.hidden=role!=="instructor";' .
    'cards.forEach(function(c){c.classList.toggle("selected",c.dataset.role===role)});}' .
    'cards.forEach(function(c){c.addEventListener("click",function(){select(c.dataset.role)})});' .
    '})();</script>';

auth_layout(
    "Join the classroom\nwith your own workspace",
    'Students get a personal attendance calendar. Instructors create classes, share codes and use the live camera roster.',
    ['Pick Student or Instructor', 'Students add 3 face photos', 'Instructors skip face setup', 'Land on your own dashboard'],
    'Already registered? Log in from the previous screen.',
    $form
);
