<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$tag = normalize_rfid_tag($_POST['rfid_tag'] ?? '');

if ($tag === '') {
    flash('error', 'No RFID tag detected. Please scan again.');
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT id, name FROM users
                       WHERE UPPER(REPLACE(rfid_tag, ' ', '')) = ?");
$stmt->execute([$tag]);
$user = $stmt->fetch();

if (!$user) {
    flash('error', 'RFID tag not registered. Received tag: [' . $tag . ']. Please compare this value with the registered RFID tag.');
    redirect('index.php');
}

$today = date('Y-m-d');
// Explicitly add +08:00 so PostgreSQL stores exact Philippine Time
$now = date('Y-m-d H:i:s') . '+08:00'; 

$stmt = $pdo->prepare('SELECT id, time_in, time_out FROM attendance WHERE user_id = ? AND log_date = ?');
$stmt->execute([$user['id'], $today]);
$attendance = $stmt->fetch();

if (!$attendance) {
    // 1. First Scan: TIME IN
    $stmt = $pdo->prepare('INSERT INTO attendance (user_id, log_date, time_in) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $today, $now]);
    flash('success', 'Welcome, ' . $user['name'] . '! Time In recorded at ' . date('h:i A', strtotime($now)) . '.');
    
} elseif (!$attendance['time_out']) {
    // 2. Second Scan: TIME OUT (Check for 15-minute interval)
    $timeInTimestamp = strtotime($attendance['time_in']);
    $currentTimeTimestamp = time();
    $diffMinutes = ($currentTimeTimestamp - $timeInTimestamp) / 60;

    if ($diffMinutes < 2) {
        // Prevent accidental quick double-scans
        flash('error', 'Please wait at least 2 minutes after Time In before recording Time Out.');
    } else {
        // Allow Time Out and use $now with +08:00 offset
        $stmt = $pdo->prepare('UPDATE attendance SET time_out = ? WHERE id = ?');
        $stmt->execute([$now, $attendance['id']]);
        flash('success', 'Goodbye, ' . $user['name'] . '! Time Out recorded at ' . date('h:i A', strtotime($now)) . '.');
    }
    
} else {
    // 3. Already completed attendance for the day
    flash('error', $user['name'] . ', you have already completed your attendance for today.');
}

redirect('index.php');