<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Monthly Report';
$assetPath = '../';

$month = max(1, min(12, (int) ($_GET['month'] ?? date('n'))));
$year = (int) ($_GET['year'] ?? date('Y'));
if ($year < 2000 || $year > 2100) {
    $year = (int) date('Y');
}

$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int) date('t', $firstDayTimestamp);
$startWeekday = (int) date('w', $firstDayTimestamp); // 0 = Sunday

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

$stmt = $pdo->prepare(
    'SELECT u.name, a.log_date, a.time_in, a.time_out
     FROM attendance a JOIN users u ON u.id = a.user_id
     WHERE EXTRACT(MONTH FROM a.log_date) = ? AND EXTRACT(YEAR FROM a.log_date) = ?
     ORDER BY a.log_date ASC, u.name ASC'
);
$stmt->execute([$month, $year]);
$details = $stmt->fetchAll();

$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0"><i class="bi bi-bar-chart"></i> Monthly Report</h4>
  <a class="btn btn-success" href="export_report.php?month=<?= $month ?>&year=<?= $year ?>">
    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV / Excel
  </a>
</div>

<form method="get" class="row g-2 mb-4 align-items-end">
  <div class="col-auto">
    <label class="form-label">Month</label>
    <select name="month" class="form-select">
      <?php for ($m = 1; $m <= 12; $m++): ?>
        <option value="<?= $m ?>" <?= $m === $month ? 'selected' : '' ?>><?= $monthNames[$m] ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Year</label>
    <select name="year" class="form-select">
      <?php for ($y = (int) date('Y'); $y >= (int) date('Y') - 5; $y--): ?>
        <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i> View</button>
  </div>
</form>

<h6 class="mb-2"><?= $monthNames[$month] ?> <?= $year ?> - Attendance Calendar</h6>
<div class="table-responsive mb-4">
<table class="table table-bordered calendar-table text-center">
  <thead class="table-dark">
    <tr><?php foreach ($weekDays as $wd): ?><th><?= $wd ?></th><?php endforeach; ?></tr>
  </thead>
  <tbody>
    <tr>
      <?php for ($i = 0; $i < $startWeekday; $i++): ?>
        <td class="empty"></td>
      <?php endfor; ?>
      <?php
      $col = $startWeekday;
      for ($day = 1; $day <= $daysInMonth; $day++):
          $names = $namesByDay[$day] ?? '';
      ?>
        <td class="calendar-day <?= $names !== '' ? 'has-attendance' : '' ?>">
          <div class="day-number"><?= $day ?></div>
          <?php if ($names !== ''): ?><div class="small text-start mt-1"><?= e($names) ?></div><?php endif; ?>
        </td>
        <?php
        $col++;
        if ($col === 7 && $day !== $daysInMonth):
            $col = 0;
            echo '</tr><tr>';
        endif;
        ?>
      <?php endfor; ?>
      <?php while ($col < 7): $col++; ?>
        <td class="empty"></td>
      <?php endwhile; ?>
    </tr>
  </tbody>
</table>
</div>

<h6 class="mb-2">Detailed Records</h6>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead class="table-dark">
    <tr><th>Name</th><th>Date</th><th>Time In</th><th>Time Out</th></tr>
  </thead>
  <tbody>
    <?php if (!$details): ?>
      <tr><td colspan="4" class="text-center text-muted">No attendance records for this period.</td></tr>
    <?php endif; ?>
    <?php foreach ($details as $row): ?>
      <tr>
        <td><?= e($row['name']) ?></td>
        <td><?= e($row['log_date']) ?></td>
        <td><?= $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-' ?></td>
        <td><?= $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : '-' ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
