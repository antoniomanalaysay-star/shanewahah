<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

$user = require_role('admin', 'instructor');
$cid = query('class_id');
$classes = safe_api('GET', '/classes') ?: [];
if ($cid === '' && $classes) {
    $cid = (string)($classes[0]['id'] ?? '');
}
$class = null;
foreach ($classes as $c) {
    if (($c['id'] ?? '') === $cid) {
        $class = $c;
        break;
    }
}
$name = $class['name'] ?? 'Class';
$status = safe_api('GET', '/attendance/session');
$active = !empty($status['active']);
$roster = $status['roster'] ?? [];

$rows = '';
foreach ($roster as $s) {
    $state = $s['status'] ?? 'absent';
    $rows .= '<div class="list-row"><span class="status-dot ' . h($state) . '"></span>' .
        '<div class="row-text"><strong>' . h($s['name'] ?? '') . '</strong></div>' .
        '<span class="roster-status ' . h($state) . '">' . h(strtoupper($state)) . ' ' . h($s['time'] ?? '') . '</span></div>';
}
if (!$rows) {
    $rows = '<div class="empty-state">No students enrolled in this class.</div>';
}

if ($active) {
    $body = info_card(
        '<div class="camera-box"><video id="scannerVideo" autoplay playsinline muted></video>' .
        '<div class="face-overlay" id="faceOverlay"></div></div>' .
        '<p class="muted small" id="scanStatus" style="margin-top:10px">Opening camera…</p>' .
        '<div class="card-actions" style="margin-top:12px">' .
        button('Reset tracking', ['variant' => 'outline', 'icon' => 'restart', 'id' => 'resetScanBtn']) .
        button('End class', ['variant' => 'danger', 'icon' => 'stop', 'id' => 'endClassBtn']) .
        '</div>'
    );
    $body .= '<div class="gap20"></div><strong style="font-size:17px">Detected (<span id="detectedCount">0</span>)</strong>' .
        '<div class="gap12"></div><div class="chip-row" id="detectedFaces"><div class="empty-state">Point the phone at your students…</div></div>' .
        '<div class="gap20"></div><strong style="font-size:17px">Live roster (' . count($roster) . ')</strong>' .
        '<div class="gap12"></div>' . info_card('<div id="scanRoster">' . $rows . '</div>');
} else {
    $body = info_card(
        '<h2>Start the class first</h2><div class="gap12"></div>' .
        '<p class="muted">Students are marked Present before the Late time and Late after it. Everyone not seen when the class ends is marked Absent.</p>' .
        '<div class="gap16"></div>' .
        button('Start class', ['variant' => 'primary', 'icon' => 'play', 'id' => 'startClassBtn'])
    );
}

$panel = '<div id="scanPanel" data-class-id="' . h($cid) . '" data-active="' . ($active ? '1' : '0') . '">' .
    error_banner($_SESSION['api_error'] ?? null) . $body . '</div>';
$appBar = '<header class="appbar"><div class="appbar-titles"><strong>Scan · ' . h($name) . '</strong></div>' .
    '<div class="spacer"></div><a class="icon-btn" href="scan.php?class_id=' . rawurlencode($cid) . '" title="Refresh roster">' . icon('refresh', 22) . '</a></header>';
page_document('Scan · ' . $name, $appBar . '<main class="class-body">' . $panel . '</main>');
