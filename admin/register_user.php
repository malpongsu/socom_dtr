<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Register RFID User';
$assetPath = '../';
$error = null;
$success = flash('success');

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
                                   WHERE UPPER(REPLACE(rfid_tag, ' ', '')) = ?");
            $stmt->execute([$rfidTag]);
            if ($stmt->fetch()) {
                $error = 'This RFID tag is already registered to another user.';
            } else {
                try {
                    $stmt = $pdo->prepare('INSERT INTO users (name, rfid_tag, department) VALUES (?, ?, ?)');
                    $stmt->execute([$name, $rfidTag, $department !== '' ? $department : null]);
                    flash('success', 'User "' . $name . '" registered successfully.');
                    redirect('register_user.php');
                } catch (PDOException $e) {
                    $error = 'This RFID tag is already registered to another user.';
                }
            }
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3 text-center"><i class="bi bi-person-badge"></i> Register User (RFID Tag)</h4>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Department (optional)</label>
            <input type="text" name="department" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">RFID Tag</label>
            <input type="text" name="rfid_tag" id="rfid_tag" class="form-control" placeholder="Scan or type the tag ID" required autocomplete="off">
            <div class="form-text">Tap the tag on the RFID reader to auto-fill this field.</div>
          </div>
          <button type="submit" class="btn btn-dark w-100">Register User</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
