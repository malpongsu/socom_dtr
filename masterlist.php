<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Masterlist';
$assetPath = '';

$stmt = $pdo->query('SELECT id, name, department, created_at FROM users ORDER BY name ASC');
$users = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<h5 class="mb-3"><i class="bi bi-people"></i> Masterlist of Registered Users</h5>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead class="table-dark">
    <tr><th>#</th><th class="text-center">Name</th><th>Department</th><th>Date Registered</th></tr>
  </thead>
  <tbody>
    <?php if (!$users): ?>
      <tr><td colspan="4" class="text-center text-muted">No registered users yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($users as $i => $u): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td class="text-center"><?= e($u['name']) ?></td>
        <td><?= e($u['department'] ?: '-') ?></td>
        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
