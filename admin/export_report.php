<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$month = max(1, min(12, (int) ($_GET['month'] ?? date('n'))));
$year = (int) ($_GET['year'] ?? date('Y'));
if ($year < 2000 || $year > 2100) {
    $year = (int) date('Y');
}

$stmt = $pdo->prepare(
    "SELECT EXTRACT(DAY FROM a.log_date) AS d, STRING_AGG(u.name, ', ' ORDER BY u.name) AS names
     FROM attendance a JOIN users u ON u.id = a.user_id
     WHERE EXTRACT(MONTH FROM a.log_date) = ? AND EXTRACT(YEAR FROM a.log_date) = ?
     GROUP BY EXTRACT(DAY FROM a.log_date)"
);
$stmt->execute([$month, $year]);
$namesByDay = [];
foreach ($stmt->fetchAll() as $row) {
    $namesByDay[(int) $row['d']] = $row['names'];
}

$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int) date('t', $firstDayTimestamp);
$startWeekday = (int) date('w', $firstDayTimestamp);
$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

$filename = 'attendance_calendar_' . $year . '_' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fputcsv($output, [$monthNames[$month] . ' ' . $year . ' - Attendance Calendar']);
fputcsv($output, $weekDays);

$calendarRow = array_fill(0, 7, '');
$weekday = $startWeekday;
for ($day = 1; $day <= $daysInMonth; $day++) {
    $calendarRow[$weekday] = (string) $day;
    if (!empty($namesByDay[$day])) {
        $calendarRow[$weekday] .= "\n" . $namesByDay[$day];
    }

    $weekday++;
    if ($weekday === 7 || $day === $daysInMonth) {
        fputcsv($output, $calendarRow);
        $calendarRow = array_fill(0, 7, '');
        $weekday = 0;
    }
}

fclose($output);
exit;
