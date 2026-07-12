<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if ($audit): ?>
<!-- Audit Header -->
<div class="audit-header data-card mb-4">
    <div class="audit-header-main">
        <h2 class="audit-title"><?= e($audit['title']) ?></h2>
        <p class="audit-meta">
            <?= e($audit['department']) ?> &middot;
            <?= e(date('j M', strtotime($audit['start_date']))) ?> – <?= e(date('j M Y', strtotime($audit['end_date']))) ?>
        </p>
        <div class="audit-auditors">
            <span class="text-muted">Auditors:</span>
            <?php foreach ($audit['auditors'] as $auditor): ?>
            <span class="auditor-badge"><?= e($auditor) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (($audit['flagged_count'] ?? 0) > 0): ?>
    <div class="audit-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong><?= e((string) $audit['flagged_count']) ?> Assets Flagged</strong>
            <span>Discrepancy report generated automatically</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Checklist Table -->
<div class="data-card mb-4">
    <h2 class="data-card-title mb-3">Checklist</h2>
    <div class="table-responsive">
        <table class="table asset-table">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Expected Location</th>
                    <th>Verification</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audit['checklist'] as $item): ?>
                <tr>
                    <td>
                        <span class="asset-tag"><?= e($item['asset_code']) ?></span>
                        <?= e($item['asset_name']) ?>
                    </td>
                    <td><?= e($item['expected_location']) ?></td>
                    <td><?= verificationBadge($item['verification']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($user['role'] ?? '') === 'admin' && ($audit['status'] ?? '') === 'open'): ?>
<form method="post" action="index.php?page=audit&action=close">
    <input type="hidden" name="audit_id" value="<?= e((string) ($audit['id'] ?? '')) ?>">
    <button type="submit" class="btn btn-primary" onclick="return confirm('Close this audit cycle?')">
        <i class="bi bi-check-circle me-1"></i>Close Audit Cycle
    </button>
</form>
<?php endif; ?>
<?php endif; ?>
