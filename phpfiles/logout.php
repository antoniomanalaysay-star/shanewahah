<?php
require_once __DIR__ . '/core.php';
logout_user();
redirect('login.php?logged_out=1');
