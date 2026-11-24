<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_user();
$cid = query('id');
if ($cid === '') {
    redirect('index.php');
}
$c = safe_api('GET', '/classes/' . rawurlencode($cid));
if (!is_array($c)) {
    flash('You do not have access to this class.', true);
    redirect('index.php');
}
$isStaff = in_array($user['role'] ?? '', ['admin', 'instructor'], true);
$allowedTabs = $isStaff
    ? ['announcements' => 'Announcements', 'students' => 'Students & Attendance', 'times' => 'Attendance times', 'live' => 'Live camera']
    : ['announcements' => 'Announcements', 'attendance' => 'My attendance'];
$tab = array_key_exists(query('tab'), $allowedTabs) ? query('tab') : array_key_first($allowedTabs);
$month = preg_match('/^\d{4}-\d{2}$/', query('month')) ? query('month') : date('Y-m');
$filter = query('status');
$error = '';
$back = 'class.php?id=' . rawurlencode($cid) . '&tab=' . $tab . '&month=' . $month . ($filter ? '&status=' . urlencode($filter) : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    [$ok, $message] = perform_action(function () use ($action, $cid, $c) {
        if ($action === 'post_announcement') {
            $title = input('title');
            $body = input('body');
            if ($title === '' || $body === '') {
                throw new ApiException('Add a title and a message.');
            }
            return api('POST', '/classes/' . rawurlencode($cid) . '/announcements', ['title' => $title, 'body' => $body]);
        }
        if ($action === 'delete_announcement') {
            return api('DELETE', '/classes/' . rawurlencode($cid) . '/announcements/' . rawurlencode(input('announcement_id')));
        }
        if ($action === 'save_times') {
            return api('PUT', '/classes/' . rawurlencode($cid), [
                'name' => $c['name'] ?? '',
                'description' => $c['description'] ?? '',
                'section' => $c['section'] ?? 'General',
                'instructor_id' => $c['instructor_id'] ?? null,
                'present_time' => valid_time(input('present_time')) ? input('present_time') : '',
                'late_time' => valid_time(input('late_time')) ? input('late_time') : '',
                'end_time' => valid_time(input('end_time')) ? input('end_time') : '',
            ]);
        }
        if ($action === 'remove_student') {
            return api('DELETE', '/classes/' . rawurlencode($cid) . '/students/' . rawurlencode(input('student_id')));
        }
        if ($action === 'leave_class') {
            return api('POST', '/classes/' . rawurlencode($cid) . '/leave', []);
        }
        if ($action === 'excuse') {
            $date = input('date');
            $note = input('note');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                throw new ApiException('Pick a valid date.');
            }
            if ($note === '') {
                throw new ApiException('Write a short note for your excuse.');
            }
            return api('POST', '/attendance/excuse', ['class_id' => $cid, 'date' => $date, 'note' => $note]);
        }
        return api('DELETE', '/attendance/excuse/' . rawurlencode($cid) . '/' . rawurlencode(input('day')));
    });
    if ($ok) {
        $messages = [
            'post_announcement' => 'Announcement posted.',
            'delete_announcement' => 'Announcement deleted.',
            'save_times' => 'Times saved. Present / Late / Absent now follow this schedule.',
            'remove_student' => 'Student removed.',
            'leave_class' => 'You left the class.',
            'excuse' => 'Excuse letter saved',
            'delete_note' => 'Note deleted.',
        ];
        if ($action === 'leave_class') {
            flash($messages[$action]);
            redirect(($user['role'] ?? '') === 'admin' ? 'admin.php' : ($isStaff ? 'instructor.php' : 'student.php'));
        }
        flash($messages[$action] ?? 'Saved.');
        redirect($back);
    }
    $error = $message;
}

$announcements = safe_api('GET', '/classes/' . rawurlencode($cid) . '/announcements') ?: [];
$excuses = safe_api('GET', '/attendance/excuses/' . rawurlencode($cid)) ?: [];
$students = $isStaff ? (safe_api('GET', '/classes/' . rawurlencode($cid) . '/students') ?: []) : [];
$studentId = (int)query('student_id');
$attendanceParams = ['month' => $month];
if ($filter !== '') {
    $attendanceParams['status'] = $filter;
}
if ($isStaff && $studentId) {
    $attendanceParams['student_id'] = $studentId;
}
$attendance = safe_api('GET', '/attendance/class/' . rawurlencode($cid) . '?' . http_build_query($attendanceParams));

$announcementHtml = '';
if ($isStaff && $tab === 'announcements') {
    $announcementHtml .= info_card('<form method="post" action="' . h($back) . '">' .
        '<input type="hidden" name="action" value="post_announcement">' .
        field('title', 'New announcement', ['icon' => 'title', 'required' => true]) .
        '<div class="gap12"></div>' .
        field('body', 'Message', ['icon' => 'message', 'required' => true]) .
        '<div class="gap12"></div>' . button('Post', ['type' => 'submit']) . '</form>');
    $announcementHtml .= '<div class="gap18"></div>';
}
if (!$announcements) {
    $announcementHtml .= info_card('<div class="empty-state">' . ($isStaff ? 'No announcements posted yet.' : 'No announcements yet.') . '</div>');
} else {
    foreach ($announcements as $a) {
        $delete = '';
        if ($isStaff) {
            $delete = '<form method="post" action="' . h($back) . '" style="display:inline">' .
                '<input type="hidden" name="action" value="delete_announcement">' .
                '<input type="hidden" name="announcement_id" value="' . h($a['id'] ?? '') . '">' .
                '<button type="submit" class="text-btn tone-danger">Delete</button></form>';
        }
        $announcementHtml .= info_card('<div class="list-row"><div class="row-text"><strong>' . h($a['title'] ?? '') . '</strong>' .
            '<small>' . h(($a['author'] ?? '') . ' · ' . ($a['date'] ?? '')) . '</small></div>' . $delete . '</div>' .
            '<div class="gap12"></div><p>' . nl2br(h($a['body'] ?? '')) . '</p>');
        $announcementHtml .= '<div class="gap12"></div>';
    }
}

$studentsPane = '';
if ($isStaff && $tab === 'students') {
    $options = '<option value="">Choose a student…</option>';
    foreach ($students as $s) {
        $options .= '<option value="' . h($s['id'] ?? '') . '"' . ((int)($s['id'] ?? 0) === $studentId ? ' selected' : '') . '>' . h($s['name'] ?? '') . '</option>';
    }
    $list = '';
    foreach ($students as $s) {
        $remove = open_dialog_button('Remove', 'outline-danger', dialog_target('removeDialog', [
            'student_id' => $s['id'] ?? '', 'message' => 'Remove ' . ($s['name'] ?? '') . ' from this class?',
        ]));
        $list .= '<div class="list-row"><span class="circle-avatar">' . h(strtoupper(substr((string)($s['name'] ?? '?'), 0, 1))) . '</span>' .
            '<div class="row-text"><strong>' . h($s['name'] ?? '') . '</strong><small>' . h($s['section'] ?? '') . '</small></div>' . $remove . '</div>';
    }
    if (!$list) {
        $list = '<div class="empty-state">No students have joined yet. Share the class code.</div>';
    }
    $detail = '<div class="empty-state">Choose a student to see their attendance calendar.</div>';
    if ($studentId) {
        $student = null;
        foreach ($students as $s) {
            if ((int)($s['id'] ?? 0) === $studentId) {
                $student = $s;
                break;
            }
        }
        $name = $student['name'] ?? ($attendance['student']['name'] ?? 'Student');
        $detail = '<strong style="font-size:19px">' . h($name) . '</strong>' .
            '<p class="muted small" style="margin-top:4px">' . h(($c['name'] ?? '') . ' · ' . ($c['section'] ?: 'General')) . '</p>' .
            '<div class="gap16"></div>' .
            (is_array($attendance) ? calendar_html($attendance, $month, 'class.php?id=' . rawurlencode($cid) . '&tab=students&student_id=' . $studentId, $filter) : info_card('<div class="empty-state">Could not load attendance.</div>'));
        $notes = '';
        foreach ($excuses as $x) {
            if ((int)($x['user_id'] ?? 0) !== $studentId) {
                continue;
            }
            $notes .= '<div class="list-row"><strong>' . h($x['date'] ?? '') . '</strong><span class="muted">' . h($x['note'] ?? '') . '</span></div>';
        }
        if ($notes) {
            $detail .= '<div class="gap16"></div>' . info_card('<h2>Private student notes</h2><div class="gap12"></div>' . $notes);
        }
    }
    $studentsPane = '<div class="students-layout">' .
        info_card('<h2>Students</h2><div class="gap12"></div>' .
            '<div class="field"><select id="studentSelect" onchange="location.href=this.value">' . $options . '</select><label>Pick a student</label></div>' .
            '<div class="gap12"></div>' . $list) .
        info_card($detail) . '</div>';
}

$timesPane = '';
if ($isStaff && $tab === 'times') {
    $timesPane = info_card('<h2>How attendance is decided</h2><div class="gap12"></div>' .
        '<div class="rule-row"><span class="rule-dot present-dot"></span><div><strong>Present · Start</strong><p class="muted small">Recognized from the start time until late begins.</p></div></div>' .
        '<div class="rule-row"><span class="rule-dot late-dot"></span><div><strong>Late</strong><p class="muted small">Recognized from the late time until the class ends.</p></div></div>' .
        '<div class="rule-row"><span class="rule-dot absent-dot"></span><div><strong>Absent</strong><p class="muted small">Nothing detected by the end time — unless a student has an approved excuse.</p></div></div>') .
        '<div class="gap18"></div>' .
        info_card('<form method="post" action="' . h($back) . '"><input type="hidden" name="action" value="save_times">' .
            '<div class="time-fields">' .
            field('present_time', 'Start / PresentTime', ['type' => 'time', 'icon' => 'time', 'value' => $c['present_time'] ?? '']) .
            field('late_time', 'LateTime', ['type' => 'time', 'icon' => 'time', 'value' => $c['late_time'] ?? '']) .
            field('end_time', 'EndTime', ['type' => 'time', 'icon' => 'time', 'value' => $c['end_time'] ?? '']) .
            '</div><p class="muted small" style="margin-top:12px">The camera marks Present or Late on detection. When you end the class, everyone without a detection is saved as Absent; excuse letters are always exempt.</p>' .
            '<div class="gap16"></div>' . button('Save times', ['type' => 'submit']) . '</form>');
}

$livePane = '';
if ($isStaff && $tab === 'live') {
    $livePane = live_panel_html([$c], (string)($c['id'] ?? ''), is_array($status = safe_api('GET', '/attendance/session')) ? $status : null, h($back));
}

$attendancePane = '';
if (!$isStaff && $tab === 'attendance') {
    $attendancePane = ($attendance['student']['name'] ?? '')
        ? '<strong style="font-size:17px">Attendance for ' . h($attendance['student']['name']) . '</strong><div class="gap12"></div>' : '';
    $attendancePane .= is_array($attendance)
        ? calendar_html($attendance, $month, 'class.php?id=' . rawurlencode($cid) . '&tab=attendance', $filter, true)
        : info_card('<div class="empty-state">Could not load attendance.</div>');
    $notes = '';
    foreach ($excuses as $x) {
        $notes .= '<div class="list-row"><div class="row-text"><strong style="color:#F5C518">' . h($x['date'] ?? '') . '</strong>' .
            '<span class="muted">' . h($x['note'] ?? '') . '</span></div>' .
            '<form method="post" action="' . h($back) . '"><input type="hidden" name="action" value="delete_note">' .
            '<input type="hidden" name="day" value="' . h($x['date'] ?? '') . '">' .
            '<button type="submit" class="icon-btn" title="Delete note">' . icon('delete', 18, '#FF6B6B') . '</button></form></div>';
    }
    $attendancePane .= '<div class="gap16"></div>' . info_card('<h2>Your saved notes</h2><div class="gap12"></div>' .
        ($notes ?: '<div class="empty-state">No private notes saved yet.</div>'));
}

$content = error_banner($error ?: null);
if ($tab === 'announcements') {
    $content .= $announcementHtml;
} elseif ($tab === 'students') {
    $content .= $studentsPane;
} elseif ($tab === 'times') {
    $content .= $timesPane;
} elseif ($tab === 'live') {
    $content .= $livePane;
} else {
    $content .= $attendancePane;
}

$actions = '';
$dialogs = '';
if (!$isStaff) {
    $actions = open_dialog_button('Leave class', 'outline-danger', dialog_target('leaveDialog', ['title' => 'Leave class?']));
    $dialogs .= dialog('leaveDialog', 'Leave class?',
        '<p>Your private notes in this class will be removed. You will need the class code to join again.</p>' .
        '<form method="post" action="' . h($back) . '"><input type="hidden" name="action" value="leave_class">' .
        '<div class="dialog-actions">' . dialog_button('Cancel', 'text', "closeDialog('leaveDialog')") .
        dialog_button('Leave', 'danger', '', 'submit') . '</div></form>');

    $defaultDate = date('Y-m-d', mktime(0, 0, 0, (int)substr($month, 5, 2), 1, (int)substr($month, 0, 4)));
    $dialogs .= dialog('excuseDialog', 'Excuse letter',
        '<p class="muted">For ' . h($defaultDate) . '</p>' .
        '<form method="post" action="' . h($back) . '"><input type="hidden" name="action" value="excuse">' .
        field('date', 'Date', ['type' => 'date', 'value' => $defaultDate, 'required' => true]) .
        field('note', 'Reason', ['icon' => 'note', 'required' => true]) .
        '<div class="dialog-actions">' . dialog_button('Cancel', 'text', "closeDialog('excuseDialog')") .
        dialog_button('Save', 'primary', '', 'submit') . '</div></form>');
} else {
    $dialogs .= dialog('removeDialog', 'Remove student?',
        '<p data-dialog-text="message"></p>' .
        '<form method="post" action="' . h($back) . '"><input type="hidden" name="action" value="remove_student">' .
        '<input type="hidden" name="student_id" id="removeStudentId">' .
        '<div class="dialog-actions">' . dialog_button('Cancel', 'text', "closeDialog('removeDialog')") .
        dialog_button('Remove', 'danger', '', 'submit') . '</div></form>');
}

$subtitle = ($c['section'] ?: 'General') . ' · ' . ($c['instructor_name'] ?? '');
$appBar = class_app_bar((string)($c['name'] ?? ''), $subtitle, (string)($c['code'] ?? ''), tabs($allowedTabs, $tab, 'class.php?id=' . rawurlencode($cid)), $actions);
page_document((string)($c['name'] ?? ''), $appBar . '<main class="class-body">' . $content . '</main>' . $dialogs);
