<?php
declare(strict_types=1);
// Include after functions.php from any file inside the admin/ folder.

require_once __DIR__ . '/functions.php';

if (empty($_SESSION['admin_id'])) {
    flash('error', 'Please login to continue.');
    redirect('login.php');
}
