<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('instructor');
$creating = query('create') === '1';
$error = '';
$class = ['name' => '', 'description' => '', 'section' => 'General', 'present_time' => '08:00', 'late_time' => '08:15', 'end_time' => '09:00'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    $cid = input('class_id');
    foreach (['name', 'description', 'section', 'present_time', 'late_time', 'end_time'] as $key) {
        $class[$key] = input($key);
    }
    [$ok, $message] = perform_action(function () use ($action, $cid, $class) {
        if ($action === 'delete') {
            return api('DELETE', '/classes/' . rawurlencode($cid));
        }
        $body = [
            'name' => $class['name'],
            'description' => $class['description'],
            'section' => $class['section'] ?: 'General',
            'present_time' => $class['present_time'],
            'late_time' => $class['late_time'],
            'end_time' => $class['end_time'],
        ];
        if ($body['name'] === '') {
            throw new ApiException('Fill in the name field.');
        }
        if ($action === 'create') {
            return api('POST', '/classes', $body);
        }
        return api('PUT', '/classes/' . rawurlencode($cid), $body);
    });
    if ($ok) {
        $messages = [
            'create' => 'Class created! Share its code with your students.',
            'edit' => 'Class updated.',
            'delete' => 'Class deleted.',
        ];
        flash($messages[$action] ?? 'Saved.');
        redirect('instructor.php');
    }
    $error = $message;
    $creating = true;
}

$classes = safe_api('GET', '/classes') ?: [];
$cards = '';
foreach ($classes as $c) {
    $edit = dialog_target('editDialog', [
        'action' => 'edit', 'class_id' => $c['id'] ?? '', 'name' => $c['name'] ?? '',
        'description' => $c['description'] ?? '', 'section' => $c['section'] ?? '',
        'present_time' => $c['present_time'] ?? '', 'late_time' => $c['late_time'] ?? '',
        'end_time' => $c['end_time'] ?? '',
    ]);
    $delete = dialog_target('deleteDialog', [
        'action' => 'delete', 'class_id' => $c['id'] ?? '',
        'title' => 'Delete class?',
    ]);
    $cards .= class_card($c, 'class.php?id=' . rawurlencode((string)($c['id'] ?? '')), $edit, $delete);
}

$createForm = '<form method="post" action="instructor.php">' .
    '<input type="hidden" name="action" value="create">' .
    '<div class="time-fields">' .
    field('name', 'Class name', ['icon' => 'class', 'value' => $class['name'], 'required' => true, 'class' => 'wide-field']) .
    field('section', 'Section', ['icon' => 'groups', 'value' => $class['section']]) . '</div>' .
    '<div class="gap12"></div>' .
    field('description', 'Description (optional)', ['icon' => 'description', 'value' => $class['description']]) .
    '<div class="gap16"></div><div class="time-fields">' .
    field('present_time', 'Start / Present time', ['type' => 'time', 'icon' => 'time', 'value' => $class['present_time']]) .
    field('late_time', 'Late begins', ['type' => 'time', 'icon' => 'time', 'value' => $class['late_time']]) .
    field('end_time', 'Class ends', ['type' => 'time', 'icon' => 'time', 'value' => $class['end_time']]) .
    '</div><p class="muted small" style="margin-top:12px">These times decide who is Present, Late or Absent. You can change them later in the class’s Attendance times tab.</p>' .
    '<div class="gap16"></div>' . button('Create class', ['type' => 'submit']) . '</form>';

$content = page_title(
    'Instructor workspace',
    'Welcome back, ' . ($user['name'] ?? '') . '. Create classes, share the code, post announcements and check attendance.'
) .
    section_card('My classes', error_banner($error ?: null) .
        ($creating ? $createForm : '') .
        '<div class="gap16"></div>' .
        ($cards ? '<div class="class-grid">' . $cards . '</div>' : '<div class="empty-state">No classes yet. Create one above.</div>') .
        '<div class="gap16"></div>',
        $creating ? text_button('Close', 'instructor.php') : text_button('New class', 'instructor.php?create=1', ['icon' => 'add'])
    );

$editForm = '<form method="post" action="instructor.php"><input type="hidden" name="action"><input type="hidden" name="class_id">' .
    field('name', 'Class name', ['icon' => 'class', 'required' => true]) .
    field('description', 'Description', ['icon' => 'description']) .
    field('section', 'Section', ['icon' => 'groups']) .
    '<div class="dialog-actions">' . dialog_button('Cancel', 'text', "closeDialog('editDialog')") .
    dialog_button('Save changes', 'primary', '', 'submit') . '</div></form>';
$deleteForm = '<form method="post" action="instructor.php"><input type="hidden" name="action"><input type="hidden" name="class_id">' .
    '<p>This also removes its announcements and attendance records.</p>' .
    '<div class="dialog-actions">' . dialog_button('Cancel', 'text', "closeDialog('deleteDialog')") .
    dialog_button('Delete', 'danger', '', 'submit') . '</div></form>';

$dialogs = dialog('editDialog', 'Rename / edit class', $editForm) . dialog('deleteDialog', 'Delete class?', $deleteForm);
shell_page($user, 'classes', $content, $dialogs);
