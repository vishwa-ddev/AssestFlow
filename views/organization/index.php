<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="data-card">
            <h2 class="data-card-title">Departments</h2>
            <ul class="setup-list">
                <?php foreach ($departments as $d): ?>
                <li><?= e($d['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="data-card">
            <h2 class="data-card-title">Asset Categories</h2>
            <ul class="setup-list">
                <?php foreach ($categories as $c): ?>
                <li><?= e($c['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="data-card">
            <h2 class="data-card-title">Vendors</h2>
            <ul class="setup-list">
                <?php foreach ($vendors as $v): ?>
                <li><?= e($v['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
