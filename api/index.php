<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = trim($path, '/');

$pages = [
    '' => 'index.php',
    'index.php' => 'index.php',
    'masterlist.php' => 'masterlist.php',
    'rfid_scan.php' => 'rfid_scan.php',
    'admin/dashboard.php' => 'admin/dashboard.php',
    'admin/delete_user.php' => 'admin/delete_user.php',
    'admin/edit_user.php' => 'admin/edit_user.php',
    'admin/export_report.php' => 'admin/export_report.php',
    'admin/login.php' => 'admin/login.php',
    'admin/logout.php' => 'admin/logout.php',
    'admin/register_admin.php' => 'admin/register_admin.php',
    'admin/register_user.php' => 'admin/register_user.php',
    'admin/reports.php' => 'admin/reports.php',
    'admin/users.php' => 'admin/users.php',
];

if (!isset($pages[$path])) {
    http_response_code(404);
    exit('Not Found');
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . $pages[$path];