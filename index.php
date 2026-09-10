<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Home';
$assetPath = '';

$error = flash('error');
$success = flash('success');

$stmt = $pdo->query(
    "SELECT u.name, a.log_date, a.time_in, a.time_out
     FROM attendance a
     JOIN users u ON u.id = a.user_id
     ORDER BY a.log_date DESC, COALESCE(a.time_out, a.time_in) DESC
     LIMIT 15"
);
$recent = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center mb-4">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-sm scan-card">
      <div class="card-body text-center">
        <h4 class="card-title mb-3"><i class="bi bi-credit-card-2-front"></i> Tap RFID Tag</h4>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <form method="post" action="rfid_scan.php" id="scanForm">
          <input type="text" name="rfid_tag" id="rfid_tag" class="form-control form-control-lg text-center"
                 placeholder="Scan your tag..." autofocus autocomplete="off">
        </form>
        <div id="clock" class="mt-3 fs-4 fw-bold text-secondary"></div>
      </div>
    </div>
  </div>
</div>

<h5 class="mb-3"><i class="bi bi-clock-history"></i> Recent Attendance</h5>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead class="table-dark">
    <tr><th class="text-center">Name</th><th>Date</th><th>Time In</th><th>Time Out</th></tr>
  </thead>
  <tbody>
    <?php if (!$recent): ?>
      <tr><td colspan="4" class="text-center text-muted">No attendance records yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($recent as $row): ?>
      <tr>
        <td class="text-center"><?= e($row['name']) ?></td>
        <td><?= e($row['log_date']) ?></td>
        <td><?= $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-' ?></td>
        <td><?= $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : '-' ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
