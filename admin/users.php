<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Registered Users';
$assetPath = '../';
$success = flash('success');
$error = flash('error');

$stmt = $pdo->query('SELECT id, name, rfid_tag, department, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-people"></i> Registered Users</h4>
  <a href="register_user.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Register New User</a>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead class="table-dark">
    <tr><th>ID</th><th>Name</th><th>RFID Tag</th><th>Department</th><th>Created</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php if (!$users): ?>
      <tr><td colspan="6" class="text-center text-muted">No registered users yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int) $u['id'] ?></td>
        <td><?= e($u['name']) ?></td>
        <td><code><?= e($u['rfid_tag']) ?></code></td>
        <td><?= e($u['department'] ?: '-') ?></td>
        <td><?= date('M d, Y h:i A', strtotime($u['created_at'])) ?></td>
        <td class="text-nowrap">
          <a href="edit_user.php?id=<?= (int) $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
          <form method="post" action="delete_user.php" class="d-inline" onsubmit="return confirm('Delete user \'<?= e($u['name']) ?>\'? This will also remove their attendance history.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
