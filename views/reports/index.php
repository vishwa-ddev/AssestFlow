<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Chart Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="data-card chart-card">
            <h2 class="data-card-title">Utilization by Department</h2>
            <div class="chart-placeholder">
                <div class="bar-chart-demo">
                    <?php foreach ($utilization as $dept): ?>
                    <div class="bar-item">
                        <div class="bar-fill" style="height: <?= min(100, ((int)($dept['total'] ?? 0)) * 2) ?>%"></div>
                        <span class="bar-label"><?= e($dept['name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="data-card chart-card">
            <h2 class="data-card-title">Maintenance Frequency</h2>
            <div class="chart-placeholder line-chart-demo">
                <svg viewBox="0 0 300 120" class="line-chart-svg">
                    <polyline points="0,100 50,80 100,90 150,50 200,60 250,30 300,40"
                              fill="none" stroke="#2563eb" stroke-width="2"/>
                </svg>
                <span class="chart-placeholder-label">Line chart placeholder</span>
            </div>
        </div>
    </div>
</div>

<!-- Lists -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="data-card">
            <h2 class="data-card-title">Most Used Assets</h2>
            <ul class="report-list">
                <?php foreach ($mostUsed as $item): ?>
                <li>
                    <span class="report-item-name"><?= e($item['name']) ?></span>
                    <span class="report-item-detail"><?= e($item['count']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="data-card">
            <h2 class="data-card-title">Idle Assets</h2>
            <ul class="report-list">
                <?php foreach ($idleAssets as $item): ?>
                <li>
                    <span class="report-item-name"><?= e($item['name']) ?></span>
                    <span class="report-item-detail text-warning"><?= e($item['detail']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="data-card">
            <h2 class="data-card-title">Maintenance Due</h2>
            <ul class="report-list">
                <?php foreach ($maintenanceDue as $item): ?>
                <li>
                    <span class="report-item-name"><?= e($item['name']) ?></span>
                    <span class="report-item-detail text-danger"><?= e($item['detail']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php if (($user['role'] ?? '') === 'admin'): ?>
<a href="index.php?page=reports&action=export" class="btn btn-primary">
    <i class="bi bi-download me-1"></i>Export Report
</a>
<?php endif; ?>
