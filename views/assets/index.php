<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Search & Actions -->
<div class="module-toolbar">
    <form method="get" action="index.php" class="search-form">
        <input type="hidden" name="page" value="assets">
        <div class="search-input-group">
            <i class="bi bi-search"></i>
            <input type="text" name="q" class="form-control" placeholder="Search by Tag, Name, Serial Number, or QR Code"
                   value="<?= e($filters['q']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
    <a href="index.php?page=assets&action=register" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Register Asset
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="get" action="index.php" class="filter-bar">
    <input type="hidden" name="page" value="assets">
    <?php if ($filters['q']): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>
    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= e((string) $cat['id']) ?>" <?= ($filters['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= e($cat['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach (['available', 'allocated', 'maintenance', 'inactive'] as $s): ?>
        <option value="<?= e($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>>
            <?= e(ucfirst($s)) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <select name="department" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
        <option value="<?= e((string) $dept['id']) ?>" <?= ($filters['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
            <?= e($dept['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- Asset Table -->
<div class="data-card">
    <div class="table-responsive">
        <table class="table asset-table">
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assets)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No assets found.</td></tr>
                <?php else: ?>
                <?php foreach ($assets as $asset): ?>
                <tr>
                    <td><span class="asset-tag"><?= e($asset['asset_code']) ?></span></td>
                    <td><?= e($asset['name']) ?></td>
                    <td><?= e($asset['category_name'] ?? $asset['asset_type'] ?? '—') ?></td>
                    <td><?= assetStatusBadge($asset['status']) ?></td>
                    <td><?= e($asset['location'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
