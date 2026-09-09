<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, name, rfid_tag, department FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    flash('error', 'User not found.');
    redirect('users.php');
}

$pageTitle = 'Edit User';
$assetPath = '../';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $rfidTag = normalize_rfid_tag($_POST['rfid_tag'] ?? '');

        if ($name === '' || $rfidTag === '') {
            $error = 'Name and RFID Tag are required.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users
                                   WHERE UPPER(REPLACE(rfid_tag, ' ', '')) = ? AND id != ?");
            $stmt->execute([$rfidTag, $id]);
            if ($stmt->fetch()) {
                $error = 'This RFID tag is already registered to another user.';
            } else {
                try {
                    $stmt = $pdo->prepare('UPDATE users SET name = ?, rfid_tag = ?, department = ? WHERE id = ?');
                    $stmt->execute([$name, $rfidTag, $department !== '' ? $department : null, $id]);
                    flash('success', 'User updated successfully.');
                    redirect('users.php');
                } catch (PDOException $e) {
                    $error = 'This RFID tag is already registered to another user.';
                }
            }
        }
        $user = ['id' => $id, 'name' => $name, 'rfid_tag' => $rfidTag, 'department' => $department];
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3 text-center"><i class="bi bi-pencil-square"></i> Edit User</h4>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Department (optional)</label>
            <input type="text" name="department" class="form-control" value="<?= e($user['department'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">RFID Tag</label>
            <input type="text" name="rfid_tag" class="form-control" value="<?= e($user['rfid_tag']) ?>" required autocomplete="off">
          </div>
          <button type="submit" class="btn btn-dark w-100">Save Changes</button>
          <a href="users.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
