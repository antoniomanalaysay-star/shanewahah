<?php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/ui.php';

/** The brief loading screen shown by HomeGate while the Flutter session restores. */
$content = '<div class="splash"><div class="splash-logo">' . clam_logo(96) . '</div><div class="splash-spinner"></div></div>';
page_document('AtClam', $content, ['bodyClass' => 'splash-page']);
