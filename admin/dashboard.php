<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Admin Dashboard';
$assetPath = '../';

$totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalAdmins = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$todayCount = (int) $pdo->query("SELECT COUNT(*) FROM attendance WHERE log_date = CURRENT_DATE")->fetchColumn();

require __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h4>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-bg-dark shadow-sm">
      <div class="card-body">
        <div class="text-uppercase small">Total Registered Users</div>
        <div class="fs-2 fw-bold"><?= $totalUsers ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-secondary shadow-sm">
      <div class="card-body">
        <div class="text-uppercase small">Today's Attendance</div>
        <div class="fs-2 fw-bold"><?= $todayCount ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-dark shadow-sm border">
      <div class="card-body">
        <div class="text-uppercase small">Admin Accounts</div>
        <div class="fs-2 fw-bold"><?= $totalAdmins ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-3 col-6">
    <a href="register_user.php" class="btn btn-outline-light w-100 py-3"><i class="bi bi-person-badge d-block fs-3 mb-1"></i> Register RFID User</a>
  </div>
  <div class="col-md-3 col-6">
    <a href="users.php" class="btn btn-outline-light w-100 py-3"><i class="bi bi-people d-block fs-3 mb-1"></i> Registered Users</a>
  </div>
  <div class="col-md-3 col-6">
    <a href="reports.php" class="btn btn-outline-light w-100 py-3"><i class="bi bi-bar-chart d-block fs-3 mb-1"></i> Monthly Report</a>
  </div>
  <div class="col-md-3 col-6">
    <a href="register_admin.php" class="btn btn-outline-light w-100 py-3"><i class="bi bi-person-plus d-block fs-3 mb-1"></i> Register Admin</a>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
