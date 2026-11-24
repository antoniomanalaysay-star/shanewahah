<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('instructor', 'admin');
$error = '';
$old = ['name' => '', 'description' => '', 'section' => 'General', 'present_time' => '08:00', 'late_time' => '08:15', 'end_time' => '09:00'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'description', 'section', 'present_time', 'late_time', 'end_time'] as $key) {
        $old[$key] = input($key);
    }
    [$ok, $message] = perform_action(function () use ($old) {
        if ($old['name'] === '') {
            throw new ApiException('Class name is required');
        }
        foreach (['present_time', 'late_time', 'end_time'] as $key) {
            if (!valid_time($old[$key])) {
                throw new ApiException('Enter valid times in HH:MM format.');
            }
        }
        return api('POST', '/classes', [
            'name' => $old['name'],
            'description' => $old['description'],
            'section' => $old['section'] ?: 'General',
            'present_time' => $old['present_time'],
            'late_time' => $old['late_time'],
            'end_time' => $old['end_time'],
        ]);
    });
    if ($ok) {
        flash('Class created! Share its code with your students.');
        redirect(($user['role'] ?? '') === 'admin' ? 'admin.php' : 'instructor.php');
    }
    $error = $message;
}

$content = '<div class="center-form wide">' .
    page_title('New class', 'Set the schedule that decides Present, Late and Absent.') .
    info_card(error_banner($error ?: null) .
        '<form method="post" action="create_class.php">' .
        field('name', 'Class name', ['icon' => 'class', 'value' => $old['name'], 'required' => true]) .
        field('description', 'Description (optional)', ['icon' => 'description', 'value' => $old['description']]) .
        field('section', 'Section', ['icon' => 'groups', 'value' => $old['section']]) .
        '<div class="gap18"></div><strong>Attendance times</strong>' .
        '<div class="time-fields">' .
        field('present_time', 'Present before', ['type' => 'time', 'icon' => 'check', 'value' => $old['present_time']]) .
        field('late_time', 'Late after', ['type' => 'time', 'icon' => 'time', 'value' => $old['late_time']]) .
        field('end_time', 'Class ends', ['type' => 'time', 'icon' => 'stop', 'value' => $old['end_time']]) .
        '</div>' .
        '<div class="gap20"></div>' .
        button('Create class', ['variant' => 'primary', 'class' => 'btn-block', 'type' => 'submit']) .
        '</form>') . '</div>';
shell_page($user, ($user['role'] ?? '') === 'admin' ? 'people' : 'classes', $content);
