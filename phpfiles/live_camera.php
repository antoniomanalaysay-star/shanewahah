<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('admin', 'instructor');
$classes = safe_api('GET', '/classes') ?: [];
$status = safe_api('GET', '/attendance/session');
$selected = query('class_id');
if ($selected === '' && !empty($status['session']['class_id'])) {
    $selected = (string)$status['session']['class_id'];
}
if ($selected === '' && $classes) {
    $selected = (string)($classes[0]['id'] ?? '');
}

$content = live_panel_html($classes, $selected, is_array($status) ? $status : null, 'live_camera.php');
shell_page($user, 'live', $content);
