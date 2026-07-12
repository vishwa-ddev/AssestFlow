<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Dashboard Styles -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ($extraCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= e($cssFile) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<div class="dashboard-wrapper" id="dashboardWrapper">

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Left Sidebar -->
    <?php require APP_ROOT . '/views/partials/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">

        <!-- Top Bar -->
        <?php require APP_ROOT . '/views/partials/topbar.php'; ?>

        <!-- Page Content -->
        <main class="page-content">
            <?= $content ?>
        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Dashboard JS -->
<script src="assets/js/dashboard.js"></script>
<?php if (!empty($extraJs)): ?>
    <?php foreach ($extraJs as $jsFile): ?>
<script src="<?= e($jsFile) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
