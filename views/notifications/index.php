<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Tabs -->
<ul class="nav nav-tabs notification-tabs mb-4">
    <?php foreach ($tabs as $tabKey => $tabLabel): ?>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === $tabKey ? 'active' : '' ?>"
           href="index.php?page=notifications&tab=<?= e($tabKey) ?>">
            <?= e($tabLabel) ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Activity Feed -->
<div class="data-card">
    <div class="notification-feed">
        <?php foreach ($feed as $item): ?>
        <div class="notification-item">
            <div class="notification-icon">
                <i class="bi <?= e($item['icon'] ?? 'bi-circle-fill') ?>"></i>
            </div>
            <div class="notification-body">
                <p class="notification-message"><?= e($item['message']) ?></p>
                <span class="notification-time"><?= e(timeAgo($item['created_at'])) ?></span>
            </div>
            <?php if (!empty($item['badge'])): ?>
            <span class="notification-badge badge-<?= strtolower(e($item['badge'])) ?>"><?= e($item['badge']) ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
