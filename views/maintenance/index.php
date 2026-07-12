<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="kanban-board" id="kanbanBoard">
    <?php foreach ($stages as $stageKey => $stageLabel): ?>
    <div class="kanban-column" data-stage="<?= e($stageKey) ?>">
        <div class="kanban-column-header">
            <h3><?= e($stageLabel) ?></h3>
            <span class="kanban-count"><?= count($kanban[$stageKey] ?? []) ?></span>
        </div>
        <div class="kanban-cards" data-stage="<?= e($stageKey) ?>">
            <?php foreach ($kanban[$stageKey] ?? [] as $card): ?>
            <div class="kanban-card" draggable="<?= ($user['role'] ?? '') === 'admin' ? 'true' : 'false' ?>"
                 data-id="<?= e((string) ($card['id'] ?? '')) ?>">
                <div class="kanban-card-tag"><?= e($card['asset_code'] ?? '') ?></div>
                <div class="kanban-card-title"><?= e($card['title'] ?? $card['asset_name'] ?? '') ?></div>
                <div class="kanban-card-desc">
                    <?php if (!empty($card['issue_description'])): ?>
                        <?= e($card['issue_description']) ?>
                    <?php elseif (!empty($card['technician_name'])): ?>
                        Tech: <?= e($card['technician_name']) ?>
                    <?php elseif (!empty($card['resolved_at'])): ?>
                        Resolved <?= e(date('j M', strtotime($card['resolved_at']))) ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<form id="stageForm" method="post" action="index.php?page=maintenance&action=updateStage" class="d-none">
    <input type="hidden" name="id" id="stageCardId">
    <input type="hidden" name="stage" id="stageValue">
</form>

<script src="assets/js/kanban.js"></script>
