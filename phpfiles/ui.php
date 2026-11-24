<?php
require_once __DIR__ . '/core.php';

/** Inline Material-ish icons so no external assets are needed. */
function icon(string $name, int $size = 20, string $color = ''): string
{
    $paths = [
        'bell' => '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>',
        'account' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-9c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0-6c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3z"/>',
        'menu' => '<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>',
        'add' => '<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>',
        'close' => '<path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>',
        'copy' => '<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>',
        'edit' => '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>',
        'delete' => '<path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>',
        'play' => '<path d="M8 5v14l11-7z"/>',
        'stop' => '<path d="M6 6h12v12H6z"/>',
        'phone' => '<path d="M16 1H8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm-3 20h-2v-1h2v1zm3-3H8V5h8v13z"/>',
        'refresh' => '<path d="M17.65 6.35A8 8 0 0 0 12 4c-4.42 0-8 3.58-8 8s3.58 8 8 8c3.73 0 6.84-2.55 7.73-6h-2.08a6 6 0 1 1-5.65-4c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>',
        'person' => '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>',
        'class' => '<path d="M18 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 14H6V6h12v12zM8 8h8v2H8V8zm0 4h8v2H8v-2z"/>',
        'camera' => '<path d="M17 10.5V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3.5l4 4v-11l-4 4zM15 16H5V8h10v8z"/>',
        'school' => '<path d="M12 3 1 9l4 2.18v6L12 21l7-3.82v-6L23 9 12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>',
        'admin' => '<path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3zm0 2.1 6 2.25V11c0 4.08-2.55 7.8-6 8.9-3.45-1.1-6-4.82-6-8.9V6.35l6-2.25zm-1 9.2-3-3 1.4-1.4 1.6 1.6 4.1-4.1 1.4 1.4-5.5 5.5z"/>',
        'mail' => '<path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4.3 6h15.4L12 11z"/>',
        'lock' => '<path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-9h-1V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2zM8.9 6a3.1 3.1 0 0 1 6.2 0v2H8.9V6z"/>',
        'lock_reset' => '<path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-9h-1V6a5 5 0 0 0-9.7-1.6l1.8 1.1A3.1 3.1 0 0 1 15.1 6v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2z"/>',
        'at' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm-1-13h5v2h-3.3a3 3 0 1 0 0 4H16v2H9v-2h2.7a1 1 0 1 0 0-2H11V7z"/>',
        'badge' => '<path d="M20 7h-3.2A5 5 0 0 0 7.2 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM12 6a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm0 14a5 5 0 0 1-4-2h8a5 5 0 0 1-4 2z"/>',
        'title' => '<path d="M5 4v3h5.5v13h3V7H19V4H5z"/>',
        'message' => '<path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>',
        'description' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        'groups' => '<path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V20h7v-3c0-2.66-5.33-4-8-4z"/>',
        'time' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/>',
        'qr' => '<path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm8 2h2v-2h-2v2zm4-4h2v2h-2v-2zm0 6h2v2h-2v-2zm2-4h2v2h-2v-2z"/>',
        'face' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zM8.5 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM12 17.5c2.3 0 4.3-1.4 5.1-3.5H6.9c.8 2.1 2.8 3.5 5.1 3.5z"/>',
        'add_photo' => '<path d="M20 4h-4.2l-1.2-1.6h-5.2L8.2 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 14H4V6h5.2l1.2 1.6h5.2L17.8 6H20v12zm-8-3a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>',
        'verified' => '<path d="m12 2 2.4 1.8 3-.2 1 2.8 2.5 1.7-.9 2.9.9 2.9-2.5 1.7-1 2.8-3-.2L12 22l-2.4-1.8-3 .2-1-2.8L3.1 16l.9-2.9-.9-2.9L5.6 8.4l1-2.8 3 .2L12 2zm-1.2 13.6 5.6-5.6-1.4-1.4-4.2 4.2-2.1-2.1L7.3 12l3.5 3.6z"/>',
        'upload' => '<path d="M5 20h14v-2H5v2zM12 2 5.5 8.5l1.4 1.4L11 5.8V16h2V5.8l4.1 4.1 1.4-1.4L12 2z"/>',
        'note' => '<path d="M20 3H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm0 16H4V5h16v14zM6 8h12v2H6V8zm0 4h8v2H6v-2z"/>',
        'exit' => '<path d="M10 17l1.4-1.4-3-3H20v-2H8.4l3-3L10 6.2 4.2 12 10 17zM4 19h16v2H4v-2z"/>',
        'chevron_left' => '<path d="M15.4 7.4 14 6l-6 6 6 6 1.4-1.4L9.8 12l5.6-4.6z"/>',
        'chevron_right' => '<path d="M8.6 16.6 10 18l6-6-6-6-1.4 1.4 5.6 4.6-5.6 4.6z"/>',
        'expand_more' => '<path d="M7.4 8.6 12 13.2l4.6-4.6L18 10l-6 6-6-6 1.4-1.4z"/>',
        'dns' => '<path d="M7 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM5 19a2 2 0 1 0 4 0 2 2 0 0 0-4 0zm14-16H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm0 6H5V5h14v4zm0 4H5v4h14v-4z"/>',
        'error' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
        'info' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>',
        'check' => '<path d="m9 16.2-3.5-3.5L4 14.2 9 19 20 8l-1.4-1.4L9 16.2z"/>',
        'restart' => '<path d="M12 5V1L7 6l5 5V7a5 5 0 1 1-5 5H5a7 7 0 1 0 7-7z"/>',
        'wrench' => '<path d="M22.7 19 13 9.3a6 6 0 0 0-7.6-7.6l3.4 3.4-3.3 3.3-3.4-3.4A6 6 0 0 0 9.3 13l9.7 9.7a2 2 0 0 0 2.8-2.8L12.1 10.2z"/>',
        'visibility' => '<path d="M12 5C6.5 5 2.4 9 1 12c1.4 3 5.5 7 11 7s9.6-4 11-7c-1.4-3-5.5-7-11-7zm0 12c-4.1 0-7.4-3-8.5-5C4.6 10 7.9 7 12 7s7.4 3 8.5 5c-1.1 2-4.4 5-8.5 5zm0-9a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>',
        'visibility_off' => '<path d="M12 5c-1.8 0-3.5.5-5 1.3l1.5 1.5c1.1-.5 2.3-.8 3.5-.8 4.1 0 7.4 3 8.5 5-.4.8-1.2 1.9-2.3 2.8l1.4 1.4C21 14.7 22.4 12.8 23 12c-1.4-3-5.5-7-11-7zM2 3.3 3.3 2l18.7 18.7-1.3 1.3-3.2-3.2c-1.3.8-2.8 1.2-4.5 1.2-5.5 0-9.6-4-11-7 .6-1.3 1.8-3 3.5-4.4L2 3.3zm5.2 5.2c-.8.9-1.4 1.9-1.7 2.5 1.1 2 4.4 5 8.5 5 1.1 0 2.1-.2 3-.6l-1.6-1.6a4 4 0 0 1-5.2-5.2L7.2 8.5zM12 7c.7 0 1.4.1 2 .4l-4.7 4.7A4 4 0 0 1 12 7z"/>',
        'campaign' => '<path d="M18 11a3 3 0 0 0-2 2.8V18H9v4h3v-2h5v-2h5V9l-4-1v3zm-4 2a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM3 9v6h4l5 5V4L7 9H3zm2 2h2.6l2.4-2.4v6.8L7.6 13H5v-2z"/>',
    ];
    $path = $paths[$name] ?? $paths['info'];
    $style = $color ? ' style="color:' . h($color) . '"' : '';
    return '<svg class="icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"' . $style . '>' . $path . '</svg>';
}

/** The hexagon shield + graduation cap from widgets/clam_logo.dart. */
function clam_logo(int $size = 100): string
{
    return '<svg class="clam-logo" width="' . $size . '" height="' . $size . '" viewBox="0 0 120 120" aria-label="AtClam logo">
  <defs>
    <linearGradient id="lgOuter" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FFE56B"/><stop offset="0.42" stop-color="#B9E04B"/><stop offset="1" stop-color="#1F7A43"/>
    </linearGradient>
    <linearGradient id="lgCap" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#FFE98A"/><stop offset="1" stop-color="#F5C518"/>
    </linearGradient>
  </defs>
  <path d="M60 4 108 30 108 76 60 116 12 76 12 30Z" fill="url(#lgOuter)"/>
  <path d="M60 15 98 36 98 71 60 104 22 71 22 36Z" fill="#05130C"/>
  <path d="M60 34 92 47 60 60 28 47Z" fill="url(#lgCap)"/>
  <path d="M39 52.5V66c0 5.2 9.4 8.8 21 8.8s21-3.6 21-8.8V52.5" fill="none" stroke="#8FD14F" stroke-width="5" stroke-linecap="round"/>
  <path d="M92 47V62" stroke="#FFD93D" stroke-width="3" stroke-linecap="round"/><circle cx="92" cy="66" r="4" fill="#FFD93D"/>
  <path d="M45 83 60 92 75 83" fill="none" stroke="#A8E063" stroke-opacity="0.95" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="45" cy="83" r="4.4" fill="#F5C518"/><circle cx="60" cy="92" r="5" fill="#A8E063"/><circle cx="75" cy="83" r="4.4" fill="#F5C518"/>
</svg>';
}

function avatar_url(array $user): string
{
    $id = $user['id'] ?? '';
    return $id !== '' ? backend_url() . '/avatars/' . rawurlencode((string)$id) . '.jpg' : '';
}

function field(string $name, string $label, array $opts = []): string
{
    $type = $opts['type'] ?? 'text';
    $value = $opts['value'] ?? '';
    $iconName = $opts['icon'] ?? '';
    $required = !empty($opts['required']) ? ' required' : '';
    $placeholder = $opts['placeholder'] ?? ' ';
    $extra = $opts['extra'] ?? '';
    $id = $opts['id'] ?? 'f_' . preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    $prefix = $iconName ? '<span class="field-prefix">' . icon($iconName, 20, '#8FAE99') . '</span>' : '';
    $suffix = '';
    if ($type === 'password') {
        $suffix = '<button type="button" class="field-suffix pass-toggle" data-target="' . h($id) . '" aria-label="Show password">' . icon('visibility', 20, '#8FAE99') . '</button>';
    }
    return '<div class="field ' . h($opts['class'] ?? '') . '">' . $prefix .
        '<input id="' . h($id) . '" type="' . h($type) . '" name="' . h($name) . '" value="' . h($value) . '" placeholder="' . h($placeholder) . '"' . $required . ' ' . $extra . '>' .
        '<label for="' . h($id) . '">' . h($label) . '</label>' . $suffix . '</div>';
}

function button(string $label, array $opts = []): string
{
    $variant = $opts['variant'] ?? 'primary';
    $iconName = $opts['icon'] ?? '';
    $iconHtml = $iconName ? icon($iconName, 16) : '';
    $class = 'btn btn-' . $variant . ' ' . ($opts['class'] ?? '');
    $attrs = '';
    foreach (['name' => 'name', 'value' => 'value', 'onclick' => 'onclick', 'id' => 'id', 'type' => 'type', 'form' => 'form'] as $key => $attr) {
        if (isset($opts[$key])) {
            $attrs .= ' ' . $attr . '="' . h($opts[$key]) . '"';
        }
    }
    if (isset($opts['data'])) {
        foreach ($opts['data'] as $k => $v) {
            $attrs .= ' data-' . h($k) . '="' . h($v) . '"';
        }
    }
    $content = $iconHtml . '<span>' . h($label) . '</span>';
    if (!empty($opts['href'])) {
        return '<a class="' . h($class) . '" href="' . h($opts['href']) . '"' . $attrs . '>' . $content . '</a>';
    }
    return '<button class="' . h($class) . '"' . ((!empty($opts['disabled'])) ? ' disabled' : '') . $attrs . '>' . $content . '</button>';
}

function text_button(string $label, string $href, array $opts = []): string
{
    $tone = $opts['tone'] ?? 'lime';
    return '<a class="text-btn tone-' . h($tone) . ' ' . h($opts['class'] ?? '') . '" href="' . h($href) . '"' .
        (isset($opts['onclick']) ? ' onclick="' . h($opts['onclick']) . '"' : '') . '>' . h($label) . '</a>';
}

function error_banner(?string $message): string
{
    if (!$message) {
        return '';
    }
    return '<div class="error-banner">' . icon('error', 20, '#FF6B6B') . '<span>' . nl2br(h($message)) . '</span></div>';
}

function info_card(string $content, string $class = '', string $padding = ''): string
{
    return '<div class="info-card ' . h($class) . '"' . ($padding ? ' style="padding:' . h($padding) . '"' : '') . '>' . $content . '</div>';
}

function section_card(string $title, string $content, string $action = ''): string
{
    return '<section class="section-card"><div class="section-head"><h2>' . h($title) . '</h2>' .
        ($action ? '<div class="section-action">' . $action . '</div>' : '') .
        '</div><div class="section-divider"></div><div class="section-body">' . $content . '</div></section>';
}

function page_title(string $title, string $subtitle, string $actions = ''): string
{
    return '<div class="page-title"><h1>' . h($title) . '</h1><p>' . h($subtitle) . '</p>' .
        ($actions ? '<div class="page-actions">' . $actions . '</div>' : '') . '</div>';
}

function tabs(array $items, string $active, string $base): string
{
    $links = '';
    foreach ($items as $key => $label) {
        $links .= '<a class="tab ' . ($key === $active ? 'active' : '') . '" href="' . h($base . '&tab=' . urlencode($key)) . '">' . h($label) . '</a>';
    }
    return '<nav class="tabs">' . $links . '</nav>';
}

function status_chip(string $status, ?string $label = null): string
{
    $labels = ['present' => 'Present', 'late' => 'Late', 'absent' => 'Absent', 'excused' => 'Excused'];
    return '<span class="status-chip status-' . h($status) . '">' . h($label ?? $labels[$status] ?? ucfirst($status)) . '</span>';
}

function dialog(string $id, string $title, string $body, string $actions = '', string $formAction = '', string $method = 'post'): string
{
    $formOpen = $formAction ? '<form method="' . h($method) . '" action="' . h($formAction) . '">' : '';
    $formClose = $formAction ? '</form>' : '';
    return '<dialog class="flutter-dialog" id="' . h($id) . '">' . $formOpen .
        '<h3 data-dialog-text="title">' . h($title) . '</h3><div class="dialog-body">' . $body . '</div>' .
        '<div class="dialog-actions">' . $actions . '</div>' . $formClose . '</dialog>';
}

function dialog_target(string $id, array $params = []): string
{
    return '#' . $id . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
}

function open_dialog_button(string $label, string $variant, string $target): string
{
    return '<button type="button" class="btn btn-' . h($variant) . ' btn-sm" onclick="' .
        h('openDialog(' . json_encode($target, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ')') . '">' .
        h($label) . '</button>';
}

function dialog_button(string $label, string $variant = 'text', string $onclick = '', string $type = 'button'): string
{
    return '<button type="' . h($type) . '" class="btn btn-' . h($variant) . '" onclick="' . h($onclick) . '">' . h($label) . '</button>';
}

function render_flash(): string
{
    $f = take_flash();
    if (!$f) {
        return '';
    }
    return '<div class="snackbar show' . ($f['error'] ? ' error' : '') . '" id="snackbar">' . h($f['message']) . '</div>';
}

function page_document(string $title, string $content, array $opts = []): void
{
    $bodyClass = $opts['bodyClass'] ?? '';
    $flash = render_flash();
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' .
        '<title>' . h($title) . ' · AtClam</title><link rel="stylesheet" href="styles.php"></head>' .
        '<body class="' . h($bodyClass) . '">' . $content . $flash . '<script src="scripts.php"></script></body></html>';
}

/** Login / register split-screen layout from AuthLayout + AuthHero. */
function auth_layout(string $title, string $description, array $points, string $footer, string $form): void
{
    $pointHtml = '';
    foreach ($points as $i => $point) {
        $pointHtml .= '<div class="hero-point"><span>0' . ($i + 1) . '</span><p>' . h($point) . '</p></div>';
    }
    $content = '<div class="auth-layout"><aside class="auth-hero"><div class="auth-hero-inner">' .
        '<div class="brand-row">' . clam_logo(54) . '<div class="brand-name">AtClam<br><span>CLASSROOM ATTENDANCE</span></div></div>' .
        '<h1>' . h($title) . '</h1><p class="hero-desc">' . h($description) . '</p>' .
        $pointHtml . '<div class="hero-footer">' . h($footer) . '</div></div></aside>' .
        '<main class="auth-main"><div class="auth-form">' . $form . '</div></main></div>';
    page_document($title, $content, ['bodyClass' => 'auth-page']);
}

function notification_dialog(?array $data): string
{
    $items = $data['items'] ?? [];
    $rows = '';
    if (!$items) {
        $rows = '<div class="empty-state">No notifications yet.</div>';
    } else {
        foreach ($items as $item) {
            $kind = $item['kind'] ?? 'announcement';
            $rows .= '<button class="notification-item" data-id="' . h($item['id'] ?? '') . '">' .
                icon($kind === 'excuse' ? 'mail' : 'campaign', 22, !empty($item['read']) ? '#8FAE99' : '#F5C518') .
                '<span><strong>' . h($item['title'] ?? '') . '</strong><small>' . h(($item['body'] ?? '') . ' · ' . ($item['time'] ?? '')) . '</small></span></button>';
        }
    }
    return '<dialog class="notify-dialog" id="notifyDialog"><div class="notify-head"><h3>Notifications</h3>' .
        '<button type="button" class="text-btn tone-gold" id="markAllRead">Mark all read</button></div>' .
        '<div class="notify-list" id="notifyList">' . $rows . '</div></dialog>';
}

/** Desktop top-bar + side navigation from LegacyShell. */
function legacy_shell(array $user, array $nav, string $activeTitle, string $content, ?array $notify = null, string $extraDialogs = ''): void
{
    $sideItems = '';
    foreach ($nav as $item) {
        $sideItems .= '<a class="nav-item ' . (!empty($item['active']) ? 'active' : '') . '" href="' . h($item['url']) . '">' .
            icon($item['icon'] ?? 'class', 19, !empty($item['active']) ? '#F5C518' : '#8FAE99') .
            '<span>' . h($item['label']) . '</span></a>';
    }
    $unread = (int)($notify['unread'] ?? 0);
    $badge = $unread > 0 ? '<span class="badge" id="notifyBadge">' . $unread . '</span>' : '<span class="badge" id="notifyBadge" hidden></span>';
    $name = (string)($user['name'] ?? '');
    $initial = $name !== '' ? (function_exists('mb_substr') ? mb_substr($name, 0, 1) : substr($name, 0, 1)) : '?';
    $menuItems = '<button type="button" data-menu="password">Change password</button>';
    if (($user['role'] ?? '') === 'student') {
        $menuItems .= '<button type="button" data-menu="photos">My face photos</button>';
    }
    $menuItems .= '<button type="button" data-menu="server">Server address</button>' .
        '<button type="button" data-menu="logout">Log out</button>';

    $serverDialog = dialog('serverDialog', 'Server address',
        '<p class="muted">For a phone, use your computer’s LAN address, e.g. http://192.168.1.10:8000</p>' .
        field('backend_url', 'Backend URL', ['icon' => 'dns', 'value' => backend_url()]),
        dialog_button('Cancel', 'text', 'closeDialog(\'serverDialog\')') .
        dialog_button('Save', 'primary', 'saveBackendUrl()'));

    $shell = '<div class="shell"><aside class="sidebar" id="sidebar"><div class="side-brand">' . clam_logo(30) .
        '<div class="brand-name small">AtClam<br><span>CLASSROOM ATTENDANCE</span></div></div>' .
        '<div class="nav-label">WORKSPACE</div><nav class="side-nav">' . $sideItems . '</nav>' .
        '<div class="side-foot"><span class="dot"></span><span>Attendance ready<br><small>Camera and calendars are active.</small></span></div></aside>' .
        '<div class="workspace"><header class="topbar"><button type="button" class="icon-btn drawer-toggle" id="drawerToggle">' . icon('menu', 22) . '</button>' .
        clam_logo(30) . '<strong>' . h($activeTitle) . '</strong><div class="spacer"></div>' .
        '<span class="role-pill">' . h(strtoupper((string)($user['role'] ?? ''))) . '</span>' .
        '<span class="topbar-user">' . h($user['name'] ?? '') . '</span><span class="avatar">' . h(function_exists('mb_strtoupper') ? mb_strtoupper($initial) : strtoupper($initial)) . '</span>' .
        '<button type="button" class="icon-btn bell" id="bellBtn" title="Notifications">' . icon('bell', 22) . $badge . '</button>' .
        '<div class="account-wrap"><button type="button" class="icon-btn" id="accountBtn" title="Account">' . icon('account', 22, '#8FAE99') . '</button>' .
        '<div class="account-menu" id="accountMenu">' . $menuItems . '</div></div>' .
        '<a class="text-btn tone-muted logout-link" href="index.php?action=logout">Log out</a></header>' .
        '<main class="workspace-body">' . $content . '</main></div></div>' .
        notification_dialog($notify) . $serverDialog . $extraDialogs;
    page_document($activeTitle, $shell, ['bodyClass' => 'shell-page']);
}

/** Left navigation destinations for each role, from LegacyShell. */
function role_nav(string $role, string $active): array
{
    $nav = [];
    if ($role === 'admin') {
        $nav = [
            ['key' => 'people', 'label' => 'People & classes', 'url' => 'admin.php', 'icon' => 'admin'],
            ['key' => 'live', 'label' => 'Live camera', 'url' => 'live_camera.php', 'icon' => 'camera'],
            ['key' => 'profile', 'label' => 'My profile', 'url' => 'profile.php', 'icon' => 'person'],
        ];
    } elseif ($role === 'instructor') {
        $nav = [
            ['key' => 'classes', 'label' => 'My classes', 'url' => 'instructor.php', 'icon' => 'class'],
            ['key' => 'live', 'label' => 'Live camera', 'url' => 'live_camera.php', 'icon' => 'camera'],
            ['key' => 'profile', 'label' => 'My profile', 'url' => 'profile.php', 'icon' => 'person'],
        ];
    } else {
        $nav = [
            ['key' => 'classes', 'label' => 'My classes', 'url' => 'student.php', 'icon' => 'class'],
            ['key' => 'profile', 'label' => 'My profile', 'url' => 'profile.php', 'icon' => 'person'],
        ];
    }
    $title = 'AtClam';
    foreach ($nav as &$item) {
        $item['active'] = $item['key'] === $active;
        if ($item['active']) {
            $title = $item['label'];
        }
    }
    unset($item);
    return ['nav' => $nav, 'title' => $title];
}

/** Convenience wrapper used by all dashboard pages. */
function shell_page(array $user, string $active, string $content, string $extraDialogs = ''): void
{
    $menu = role_nav((string)($user['role'] ?? ''), $active);
    $notify = safe_api('GET', '/notifications');
    legacy_shell($user, $menu['nav'], $menu['title'], $content, is_array($notify) ? $notify : null, $extraDialogs);
}

/** App bar used by the per-class screens. */
function class_app_bar(string $name, string $subtitle, string $code, string $tabsHtml, string $actions = ''): string
{
    return '<header class="appbar"><div class="appbar-titles"><strong>' . h($name) . '</strong><small>' . h($subtitle) . '</small></div><div class="spacer"></div>' .
        $actions . '<button type="button" class="code-chip copy-code" data-code="' . h($code) . '">' . h($code) . icon('copy', 16, '#A8E063') . '</button></header>' . $tabsHtml;
}

function class_card(array $c, string $openUrl, string $editUrl = '', string $deleteUrl = ''): string
{
    $section = ($c['section'] ?? '') ?: 'General';
    $desc = ($c['description'] ?? '') ?: 'No description';
    $code = $c['code'] ?? '';
    $actions = '<a class="btn btn-primary btn-sm" href="' . h($openUrl) . '">Open class</a>';
    if ($editUrl) {
        if (str_starts_with($editUrl, '#')) {
            $actions .= open_dialog_button('Edit', 'outline', $editUrl);
        } else {
            $actions .= '<a class="btn btn-outline btn-sm" href="' . h($editUrl) . '">Edit</a>';
        }
    }
    if ($deleteUrl) {
        $actions .= open_dialog_button('Delete', 'outline-danger', $deleteUrl);
    }
    return '<article class="class-card"><div class="class-card-head"><strong>' . h($c['name'] ?? '') . '</strong>' .
        '<span class="lime-pill">' . h($section) . '</span></div><div class="class-card-body">' .
        '<p class="muted clamp2">' . h($desc) . '</p>' .
        '<p class="muted small">' . h($c['student_count'] ?? 0) . ' students · ' . h($c['instructor_name'] ?? '') . '</p>' .
        '<div class="code-row"><span class="code-box">' . h($code) . '</span>' .
        '<button type="button" class="text-btn tone-lime copy-code" data-code="' . h($code) . '">' . icon('copy', 16) . 'Copy</button></div>' .
        '<div class="card-actions">' . $actions . '</div></div></article>';
}

/** Live camera panel content shared by the dedicated destination and class tab. */
function live_panel_html(array $classes, string $selectedCid, ?array $status, string $formAction): string
{
    $active = !empty($status['active']);
    $options = '';
    foreach ($classes as $c) {
        $options .= '<option value="' . h($c['id'] ?? '') . '"' . (($c['id'] ?? '') === $selectedCid ? ' selected' : '') . '>' . h($c['name'] ?? '') . '</option>';
    }
    if (!$classes) {
        $options = '<option value="">Create a class first.</option>';
    }
    $pill = $active ? 'CLASS RUNNING' : 'NO CLASS RUNNING';
    $pillClass = $active ? 'lime-pill' : 'gold-pill';
    $model = !empty($status['model_ready']) ? 'Model ready' : 'Model loading…';
    $roster = $status['roster'] ?? [];
    $rows = '';
    foreach ($roster as $s) {
        $state = $s['status'] ?? 'absent';
        $rows .= '<div class="list-row"><span class="status-dot ' . h($state) . '"></span>' .
            '<div class="row-text"><strong>' . h($s['name'] ?? '') . '</strong><small>' . h($s['id'] ?? '') . '</small></div>' .
            '<span class="roster-status ' . h($state) . '">' . h(strtoupper($state)) . ' ' . h($s['time'] ?? '') . '</span></div>';
    }
    if (!$rows) {
        $rows = '<div class="empty-state">No students enrolled in this class.</div>';
    }
    $classLabel = $status['session']['name'] ?? '';
    return '<div id="livePanel" data-class-id="' . h($selectedCid) . '">' .
        page_title('Live camera', 'Start a class, then use the instructor’s phone to scan students.') .
        error_banner($_SESSION['api_error'] ?? null) .
        info_card('<div class="list-row"><span class="' . $pillClass . '" id="liveState">' . $pill . '</span>' .
            '<div class="spacer"></div><span class="muted small" id="modelState">' . h($model) . '</span></div>' .
            '<div class="gap12"></div><div class="field"><select id="liveClassSelect">' . $options . '</select><label>Class</label></div>' .
            ($classLabel ? '<p class="muted small" style="margin-top:8px">Current session: ' . h($classLabel) . '</p>' : '') .
            '<div class="card-actions" style="margin-top:14px">' .
            button('Start class & detection', ['variant' => 'primary', 'icon' => 'play', 'id' => 'startSessionBtn', 'disabled' => $active || !$selectedCid]) .
            button('End class', ['variant' => 'danger', 'icon' => 'stop', 'id' => 'stopSessionBtn', 'disabled' => !$active]) .
            button('Open phone scanner', ['variant' => 'tonal', 'icon' => 'phone', 'href' => 'scan.php' . ($selectedCid ? '?class_id=' . rawurlencode($selectedCid) : '')]) .
            '</div>') .
        '<div class="gap20"></div><strong style="font-size:17px">In the room</strong>' .
        '<p class="muted small" style="margin-top:4px">' . count($roster) . ' student(s) enrolled</p>' .
        '<div class="gap12"></div>' . info_card('<div id="liveRoster">' . $rows . '</div>') . '</div>';
}

function calendar_html(array $data, string $month, string $baseUrl, string $filter, bool $canExcuse = false): string
{
    $days = $data['days'] ?? [];
    $summary = $data['summary'] ?? [];
    [$year, $mon] = array_map('intval', explode('-', $month . '-1', 2) + [1 => 1]);
    if ($year < 2000) {
        $year = (int)date('Y');
        $mon = (int)date('m');
    }
    $firstWeekday = (int)date('w', mktime(0, 0, 0, $mon, 1, $year));
    $totalDays = (int)date('t', mktime(0, 0, 0, $mon, 1, $year));
    $monthName = date('F Y', mktime(0, 0, 0, $mon, 1, $year));
    [$py, $pm] = [$mon == 1 ? $year - 1 : $year, $mon == 1 ? 12 : $mon - 1];
    [$ny, $nm] = [$mon == 12 ? $year + 1 : $year, $mon == 12 ? 1 : $mon + 1];
    $prev = sprintf('%04d-%02d', $py, $pm);
    $next = sprintf('%04d-%02d', $ny, $nm);

    $chips = '';
    foreach (['present', 'late', 'absent', 'excused'] as $s) {
        $chips .= '<span class="status-chip status-' . $s . '">' . ucfirst($s) . ' ' . h($summary[$s] ?? 0) . '</span>';
    }
    $filterOptions = '<option value="">All days</option>';
    foreach (['present', 'late', 'absent', 'excused'] as $s) {
        $filterOptions .= '<option value="' . $s . '"' . ($filter === $s ? ' selected' : '') . '>' . ucfirst($s) . '</option>';
    }
    $grid = '<div class="calendar-grid">';
    foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $d) {
        $grid .= '<div class="cal-head">' . $d . '</div>';
    }
    for ($i = 0; $i < $firstWeekday + $totalDays; $i++) {
        if ($i < $firstWeekday) {
            $grid .= '<div></div>';
            continue;
        }
        $day = $i - $firstWeekday + 1;
        $key = sprintf('%04d-%02d-%02d', $year, $mon, $day);
        $status = $days[$key]['status'] ?? '';
        $label = isset($days[$key]) ? (string)$day : (string)$day;
        $title = isset($days[$key]) ? ($days[$key]['note'] ?? ($days[$key]['time'] ?? '')) : '';
        $grid .= '<div class="cal-day' . ($status ? ' cal-' . h($status) : '') . '"' . ($title ? ' title="' . h($title) . '"' : '') . '>' . $label . '</div>';
    }
    $grid .= '</div>';

    $filterForm = '<form method="get" class="filter-form" action="' . h($baseUrl) . '"><input type="hidden" name="month" value="' . h($month) . '">' .
        '<select name="status" onchange="this.form.submit()">' . $filterOptions . '</select></form>';
    $excuse = $canExcuse ? '<button type="button" class="btn btn-outline-lime" onclick="openDialog(\'excuseDialog\')">' . icon('note') . 'Add an excuse letter</button>' : '';
    return info_card(
        '<div class="cal-controls"><a class="icon-btn" href="' . h($baseUrl . '&month=' . $prev . ($filter ? '&status=' . urlencode($filter) : '')) . '">' . icon('chevron_left', 22, '#F5C518') . '</a>' .
        '<strong>' . h($monthName) . '</strong><a class="icon-btn" href="' . h($baseUrl . '&month=' . $next . ($filter ? '&status=' . urlencode($filter) : '')) . '">' . icon('chevron_right', 22, '#F5C518') . '</a></div>' .
        '<div class="chip-row">' . $chips . '</div>' . $filterForm,
        'calendar-card'
    ) . '<div class="gap12"></div>' . info_card($grid, 'calendar-grid-card') . ($excuse ? '<div class="gap12"></div>' . $excuse : '');
}
