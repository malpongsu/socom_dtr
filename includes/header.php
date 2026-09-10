<?php $assetPath = $assetPath ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'SOCOM Daily Time Record') ?> | SOCOM Daily Time Record</title>
<link rel="icon" type="image/jpeg" href="<?= $assetPath ?>assets/images/510257317_122094190988929257_2828329964117250830_n.jpg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= $assetPath ?>assets/css/style.css?v=2" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $assetPath ?>index.php">
      <img class="brand-logo" src="<?= $assetPath ?>assets/images/510257317_122094190988929257_2828329964117250830_n.jpg" alt="SOCOM logo">
      <span><i class="bi bi-fingerprint"></i> SOCOM Daily Time Record</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= $assetPath ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $assetPath ?>masterlist.php">Masterlist</a></li>
        <?php if (!empty($_SESSION['admin_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $assetPath ?>admin/dashboard.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $assetPath ?>admin/logout.php"><i class="bi bi-box-arrow-right"></i> Logout (<?= e($_SESSION['admin_name'] ?? '') ?>)</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $assetPath ?>admin/login.php"><i class="bi bi-shield-lock"></i> Admin Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container pb-5">
