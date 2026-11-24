<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('admin');
$tab = in_array(query('tab'), ['students', 'instructors', 'classes'], true) ? query('tab') : 'students';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    $recordId = input('record_id');
    [$ok, $message] = perform_action(function () use ($action, $recordId) {
        if ($action === 'add_user' || $action === 'edit_user') {
            $role = input('role', 'student') === 'instructor' ? 'instructor' : 'student';
            $body = [
                'name' => input('name'),
                'email' => input('email'),
                'role' => $role,
                'username' => input('username'),
                'school_id' => input('school_id'),
                'section' => input('section'),
            ];
            if (input('password') !== '') {
                $body['password'] = input('password');
            }
            if ($body['name'] === '') {
                throw new ApiException('Fill in the name field.');
            }
            if ($action === 'add_user') {
                $body['full_name'] = $body['name'];
                return api('POST', '/admin/users', $body);
            }
            return api('PUT', '/admin/users/' . rawurlencode($recordId), $body);
        }
        if ($action === 'delete_user') {
            return api('DELETE', '/admin/users/' . rawurlencode($recordId));
        }
        if ($action === 'delete_class') {
            return api('DELETE', '/admin/classes/' . rawurlencode($recordId));
        }
        $body = class_body_from_post();
        if ($body['name'] === '') {
            throw new ApiException('Fill in the name field.');
        }
        if ($action === 'add_class') {
            return api('POST', '/admin/classes', $body);
        }
        return api('PUT', '/admin/classes/' . rawurlencode($recordId), $body);
    });
    flash($ok ? (str_starts_with($action, 'delete') ? 'Deleted.' : 'Saved.') : $message, !$ok);
    redirect('admin.php?tab=' . urlencode($tab));
}

function class_body_from_post(): array
{
    $instructor = input('instructor_id');
    return [
        'name' => input('name'),
        'description' => input('description'),
        'section' => input('section', 'General') ?: 'General',
        'instructor_id' => $instructor === '' ? null : (int)$instructor,
        'present_time' => valid_time(input('present_time')) ? input('present_time') : '',
        'late_time' => valid_time(input('late_time')) ? input('late_time') : '',
        'end_time' => valid_time(input('end_time')) ? input('end_time') : '',
    ];
}

$students = safe_api('GET', '/admin/users?role=student') ?: [];
$instructors = safe_api('GET', '/admin/users?role=instructor') ?: [];
$classes = safe_api('GET', '/admin/classes') ?: [];

$stats = '<div class="stat-row">' .
    '<div class="stat-card"><span>Instructors</span><strong>' . count($instructors) . '</strong></div>' .
    '<div class="stat-card"><span>Students</span><strong>' . count($students) . '</strong></div>' .
    '<div class="stat-card"><span>Classes</span><strong>' . count($classes) . '</strong></div>' .
    '</div>';

$instructorOptions = '<option value="">Unassigned</option>';
foreach ($instructors as $inst) {
    $instructorOptions .= '<option value="' . h($inst['id'] ?? '') . '">' . h($inst['name'] ?? '') . '</option>';
}

$userDialogBody = '<div class="profile-grid">' .
    field('name', 'Full name', ['icon' => 'person', 'required' => true]) .
    field('username', 'Username', ['icon' => 'at']) . '</div>' .
    '<div class="profile-grid">' .
    field('email', 'Email', ['type' => 'email', 'icon' => 'mail', 'required' => true]) .
    field('section', 'Section', ['icon' => 'groups']) . '</div>' .
    '<div class="profile-grid">' .
    '<div class="field"><select name="role"><option value="student">Student</option><option value="instructor">Instructor</option></select><label>Role</label></div>' .
    field('password', 'Password (default: Attendance123)', ['type' => 'password', 'icon' => 'lock']) .
    '</div>' .
    field('school_id', 'School ID (optional)', ['icon' => 'badge']);

$classDialogBody = field('name', 'Class name', ['icon' => 'class', 'required' => true]) .
    field('description', 'Description', ['icon' => 'description']) .
    field('section', 'Section', ['icon' => 'groups', 'value' => 'General']) .
    '<div class="field"><select name="instructor_id">' . $instructorOptions . '</select><label>Instructor</label></div>' .
    '<p class="muted small">Times use HH:MM.</p><div class="time-fields">' .
    field('present_time', 'Present time', ['type' => 'time', 'value' => '08:00']) .
    field('late_time', 'Late time', ['type' => 'time', 'value' => '08:15']) .
    field('end_time', 'End time', ['type' => 'time', 'value' => '09:00']) . '</div>';

$addTarget = $tab === 'classes'
    ? dialog_target('classDialog', ['action' => 'add_class', 'title' => 'Add class'])
    : dialog_target('userDialog', [
        'action' => 'add_user',
        'role' => $tab === 'instructors' ? 'instructor' : 'student',
        'title' => $tab === 'instructors' ? 'Add instructor' : 'Add student',
    ]);
$addLabel = $tab === 'classes' ? 'Add class' : ($tab === 'instructors' ? 'Add instructor' : 'Add student');
$addAction = open_dialog_button('Add new', 'primary', $addTarget);

$rows = '';
if ($tab === 'classes') {
    foreach ($classes as $c) {
        $edit = open_dialog_button('Edit', 'outline', dialog_target('classDialog', [
            'action' => 'edit_class', 'title' => 'Rename / edit class', 'record_id' => $c['id'] ?? '', 'name' => $c['name'] ?? '',
            'description' => $c['description'] ?? '', 'section' => $c['section'] ?? '',
            'instructor_id' => $c['instructor_id'] ?? '', 'present_time' => $c['present_time'] ?? '',
            'late_time' => $c['late_time'] ?? '', 'end_time' => $c['end_time'] ?? '',
        ]));
        $del = open_dialog_button('Delete', 'outline-danger', dialog_target('deleteDialog', [
            'action' => 'delete_class', 'record_id' => $c['id'] ?? '',
            'message' => 'Delete class ' . ($c['name'] ?? 'this record') . '?',
        ]));
        $rows .= '<tr><td><span class="name">' . h($c['name'] ?? '') . '</span><span class="sub">' . h($c['description'] ?: 'No description') . '</span></td>' .
            '<td><span class="gold-pill">' . h($c['code'] ?? '') . '</span></td>' .
            '<td>' . h($c['instructor_name'] ?: 'Unassigned') . '</td><td>' . h($c['student_count'] ?? 0) . '</td>' .
            '<td><div class="card-actions">' . $edit . $del . '</div></td></tr>';
    }
    $thead = '<th>Class</th><th>Code</th><th>Instructor</th><th>Students</th><th>Actions</th>';
} else {
    foreach (($tab === 'instructors' ? $instructors : $students) as $p) {
        $role = $p['role'] ?? 'student';
        $face = $role === 'instructor' ? 'Not required' : (!empty($p['has_face']) ? 'Registered' : 'No photos');
        $edit = open_dialog_button('Edit', 'outline', dialog_target('userDialog', [
            'action' => 'edit_user', 'title' => 'Edit account', 'record_id' => $p['id'] ?? '', 'name' => $p['name'] ?? '',
            'username' => $p['username'] ?? '', 'email' => $p['email'] ?? '',
            'section' => $p['section'] ?? '', 'role' => $role,
        ]));
        $del = open_dialog_button('Delete', 'outline-danger', dialog_target('deleteDialog', [
            'action' => 'delete_user', 'record_id' => $p['id'] ?? '',
            'message' => 'Delete account ' . ($p['name'] ?? 'this record') . '?',
        ]));
        $rows .= '<tr><td><span class="name">' . h($p['name'] ?? '') . '</span><span class="sub">' . h($p['email'] ?? '') . '</span></td>' .
            '<td>' . h($p['username'] ?? '') . '</td><td>' . h($p['section'] ?: '—') . '</td>' .
            '<td>' . h($face) . '</td><td><div class="card-actions">' . $edit . $del . '</div></td></tr>';
    }
    $thead = '<th>Name</th><th>Username</th><th>Section</th><th>Face</th><th>Actions</th>';
}

$tabsHtml = '<div class="tab-buttons">';
foreach (['students' => 'Students', 'instructors' => 'Instructors', 'classes' => 'Classes'] as $key => $label) {
    $tabsHtml .= '<a class="tab-btn' . ($tab === $key ? ' active' : '') . '" href="admin.php?tab=' . $key . '">' . $label . '</a>';
}
$tabsHtml .= '</div>';

$content = page_title('Administration', 'Manage instructors, students and every class in the system.') .
    $stats .
    section_card('Records', error_banner($error ?: null) . $tabsHtml .
        '<div class="table-wrap"><table class="data"><thead><tr>' . $thead . '</tr></thead><tbody>' .
        ($rows ?: '<tr><td colspan="5" class="empty-state">No records yet.</td></tr>') .
        '</tbody></table></div>', $addAction);

$userForm = '<form method="post" action="admin.php"><input type="hidden" name="action"><input type="hidden" name="record_id">' .
    $userDialogBody . '<div class="dialog-actions">' .
    dialog_button('Cancel', 'text', "closeDialog('userDialog')") .
    dialog_button('Save', 'primary', '', 'submit') . '</div></form>';
$classForm = '<form method="post" action="admin.php"><input type="hidden" name="action"><input type="hidden" name="record_id">' .
    $classDialogBody . '<div class="dialog-actions">' .
    dialog_button('Cancel', 'text', "closeDialog('classDialog')") .
    dialog_button('Save', 'primary', '', 'submit') . '</div></form>';
$deleteForm = '<form method="post" action="admin.php"><input type="hidden" name="action"><input type="hidden" name="record_id">' .
    '<p data-dialog-text="message"></p><div class="dialog-actions">' .
    dialog_button('Cancel', 'text', "closeDialog('deleteDialog')") .
    dialog_button('Delete', 'danger', '', 'submit') . '</div></form>';

$dialogs = dialog('userDialog', 'Add / edit account', $userForm) .
    dialog('classDialog', 'Add / edit class', $classForm) .
    dialog('deleteDialog', 'Delete record?', $deleteForm);
shell_page($user, 'people', $content, $dialogs);
