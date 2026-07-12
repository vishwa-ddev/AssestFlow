<header class="topbar">
    <button class="btn btn-link sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-title">
        <h1 class="page-heading"><?= e($pageTitle ?? "Today's Overview") ?></h1>
    </div>

    <div class="topbar-actions">
        <a href="index.php?page=notifications" class="btn btn-light topbar-icon-btn" aria-label="Notifications">
            <i class="bi bi-bell"></i>
        </a>
        <div class="topbar-user">
            <div class="topbar-avatar">
                <?= e(strtoupper(substr($user['name'] ?? 'U', 0, 1))) ?>
            </div>
            <div class="topbar-user-info d-none d-md-block">
                <span class="topbar-user-name"><?= e($user['name'] ?? 'User') ?></span>
                <span class="topbar-user-role"><?= e(ucfirst($user['role'] ?? 'employee')) ?></span>
            </div>
        </div>
    </div>
</header>
