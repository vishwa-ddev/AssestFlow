<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Asset Selector -->
<div class="data-card mb-4">
    <form method="get" action="index.php">
        <input type="hidden" name="page" value="allocation-transfer">
        <label class="form-label fw-semibold">Asset Selector</label>
        <select name="asset_id" class="form-select" onchange="this.form.submit()">
            <option value="">Select an asset...</option>
            <?php foreach ($assets as $a): ?>
            <option value="<?= e((string) $a['id']) ?>" <?= $assetId == $a['id'] ? 'selected' : '' ?>>
                <?= e($a['asset_code']) ?> — <?= e($a['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($allocation): ?>
<div class="alert-banner mb-4" role="alert">
    <div class="alert-banner-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
    <div class="alert-banner-text">
        <strong>Already allocated to <?= e($allocation['assigned_to']) ?><?= $allocation['department'] ? ' (' . e($allocation['department']) . ')' : '' ?></strong>
        <span>— Direct reallocation is blocked. Submit a transfer request.</span>
    </div>
</div>
<?php endif; ?>

<?php if ($assetId && $allocation): ?>
<!-- Transfer Form -->
<div class="data-card mb-4">
    <h2 class="data-card-title mb-3">Transfer Form</h2>
    <form method="post" action="index.php?page=allocation-transfer&action=submit">
        <input type="hidden" name="asset_id" value="<?= e((string) $assetId) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">From Employee</label>
                <input type="text" name="from_user" class="form-control" value="<?= e($allocation['assigned_to']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">To Employee</label>
                <input type="text" name="to_user" class="form-control" placeholder="New assignee" required>
            </div>
            <div class="col-12">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Reason for transfer"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">
            <i class="bi bi-send me-1"></i>Submit Transfer Request
        </button>
    </form>
</div>
<?php elseif ($assetId && !$allocation): ?>
<div class="alert alert-info">This asset is not currently allocated. Transfer requests require an active allocation.</div>
<?php endif; ?>

<!-- Allocation History -->
<?php if ($assetId && !empty($history)): ?>
<section class="activity-section">
    <div class="activity-header">
        <h2 class="activity-title">Allocation History</h2>
    </div>
    <div class="activity-timeline">
        <?php foreach ($history as $index => $entry): ?>
        <div class="activity-item">
            <div class="activity-marker">
                <div class="activity-icon">
                    <i class="bi <?= $entry['event_type'] === 'returned' ? 'bi-arrow-return-left' : 'bi-person-check' ?>"></i>
                </div>
                <?php if ($index < count($history) - 1): ?>
                <div class="activity-line"></div>
                <?php endif; ?>
            </div>
            <div class="activity-content">
                <p class="activity-message">
                    <?php if ($entry['event_type'] === 'returned'): ?>
                        Returned by <?= e($entry['employee_name']) ?>
                        <?php if ($entry['condition_note']): ?> — Condition <?= e($entry['condition_note']) ?><?php endif; ?>
                    <?php else: ?>
                        Allocated to <?= e($entry['employee_name']) ?>
                        <?php if ($entry['department']): ?> — <?= e($entry['department']) ?><?php endif; ?>
                    <?php endif; ?>
                </p>
                <span class="activity-time"><?= e(date('M d', strtotime($entry['created_at']))) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
