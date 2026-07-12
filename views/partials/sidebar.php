<?php
$navItems    = getNavItems();
$currentPage = $currentPage ?? 'dashboard';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="index.php?page=dashboard" class="sidebar-brand-link">
            <div class="sidebar-logo">AF</div>
            <span class="sidebar-brand-text">AssetFlow</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <?php foreach ($navItems as $item): ?>
            <li class="nav-item">
                <a href="index.php?page=<?= e($item['page']) ?>"
                   class="nav-link <?= $currentPage === $item['page'] ? 'active' : '' ?>">
                    <i class="bi <?= e($item['icon']) ?>"></i>
                    <span><?= e($item['label']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="index.php?page=logout" class="sidebar-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
        </a>
    </div>
</aside>
