<?php
/**
 * Dashboard - Today's Overview
 */
?>

<!-- Flash Messages -->
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- KPI Cards - Row 1 -->
<div class="row g-4 mb-4">
    <?php foreach (array_slice($kpis, 0, 3) as $kpi): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-<?= e($kpi['color']) ?>">
                <i class="bi <?= e($kpi['icon']) ?>"></i>
            </div>
            <div class="kpi-details">
                <span class="kpi-value"><?= e((string) $kpi['value']) ?></span>
                <span class="kpi-label"><?= e($kpi['title']) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- KPI Cards - Row 2 -->
<div class="row g-4 mb-4">
    <?php foreach (array_slice($kpis, 3, 3) as $kpi): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-<?= e($kpi['color']) ?>">
                <i class="bi <?= e($kpi['icon']) ?>"></i>
            </div>
            <div class="kpi-details">
                <span class="kpi-value"><?= e((string) $kpi['value']) ?></span>
                <span class="kpi-label"><?= e($kpi['title']) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Overdue Alert Banner -->
<?php if ($overdueCount > 0): ?>
<div class="alert-banner" role="alert">
    <div class="alert-banner-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="alert-banner-text">
        <strong><?= e((string) $overdueCount) ?> Assets Overdue For Return</strong>
        <span>— Flagged For Follow-up</span>
    </div>
</div>
<?php endif; ?>

<!-- Quick Action Buttons -->
<div class="action-buttons">
    <a href="index.php?page=assets&action=register" class="btn btn-primary btn-action">
        <i class="bi bi-plus-circle me-2"></i>Register Asset
    </a>
    <a href="index.php?page=resource-booking&action=book" class="btn btn-primary btn-action">
        <i class="bi bi-calendar-plus me-2"></i>Book Resource
    </a>
    <a href="index.php?page=maintenance&action=raise" class="btn btn-primary btn-action">
        <i class="bi bi-wrench-adjustable me-2"></i>Raise Maintenance Request
    </a>
</div>

<!-- Recent Activity -->
<section class="activity-section">
    <div class="activity-header">
        <h2 class="activity-title">Recent Activity</h2>
        <a href="index.php?page=notifications" class="activity-view-all">View All</a>
    </div>

    <div class="activity-timeline">
        <?php foreach ($recentActivity as $index => $activity): ?>
        <div class="activity-item">
            <div class="activity-marker">
                <div class="activity-icon">
                    <i class="bi <?= e($activity['icon'] ?? 'bi-circle-fill') ?>"></i>
                </div>
                <?php if ($index < count($recentActivity) - 1): ?>
                <div class="activity-line"></div>
                <?php endif; ?>
            </div>
            <div class="activity-content">
                <p class="activity-message"><?= e($activity['message']) ?></p>
                <span class="activity-time">
                    <?= e(date('M j, Y \a\t g:i A', strtotime($activity['created_at']))) ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
