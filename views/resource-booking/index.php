<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if ($conflict): ?>
<div class="alert-banner mb-4" role="alert">
    <div class="alert-banner-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
    <div class="alert-banner-text">
        <strong>Conflict warning</strong>
        <span>— Requested slot overlaps with existing booking.</span>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="data-card">
            <h2 class="data-card-title mb-3">Book a Slot</h2>
            <form method="post" action="index.php?page=resource-booking&action=book" id="bookingForm">
                <div class="mb-3">
                    <label class="form-label">Resource</label>
                    <select name="resource_id" class="form-select" id="resourceSelect" required>
                        <?php foreach ($resources as $r): ?>
                        <option value="<?= e((string) $r['id']) ?>" data-name="<?= e($r['name']) ?>"
                            <?= $resourceId == $r['id'] ? 'selected' : '' ?>>
                            <?= e($r['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="resource_name" id="resourceName"
                           value="<?= e($resources[0]['name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="booking_date" class="form-control" value="<?= e($date) ?>" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control" id="startTime" value="09:30" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" class="form-control" id="endTime" value="10:30" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="bookBtn" <?= $conflict ? 'disabled' : '' ?>>
                    <i class="bi bi-calendar-plus me-1"></i>Book Slot
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="data-card">
            <div class="data-card-header">
                <h2 class="data-card-title">Booking Calendar — <?= e($date) ?></h2>
                <form method="get" class="d-flex gap-2">
                    <input type="hidden" name="page" value="resource-booking">
                    <input type="hidden" name="resource_id" value="<?= e((string) $resourceId) ?>">
                    <input type="date" name="date" class="form-control form-control-sm" value="<?= e($date) ?>"
                           onchange="this.form.submit()">
                </form>
            </div>

            <div class="booking-timeline">
                <?php
                $hours = range(9, 17);
                foreach ($hours as $hour):
                    $slotStart = sprintf('%02d:00', $hour);
                    $slotEnd   = sprintf('%02d:00', $hour + 1);
                    $booked    = null;
                    foreach ($bookings as $b) {
                        $bStart = substr($b['start_time'], 0, 5);
                        $bEnd   = substr($b['end_time'], 0, 5);
                        if ($bStart < $slotEnd && $bEnd > $slotStart) {
                            $booked = $b;
                            break;
                        }
                    }
                ?>
                <div class="timeline-slot <?= $booked ? 'slot-booked' : 'slot-free' ?>">
                    <span class="slot-time"><?= e($slotStart) ?></span>
                    <div class="slot-content">
                        <?php if ($booked): ?>
                        <span class="slot-badge">Booked</span>
                        <span class="slot-user"><?= e($booked['booked_by']) ?></span>
                        <span class="slot-range"><?= e(substr($booked['start_time'], 0, 5)) ?>–<?= e(substr($booked['end_time'], 0, 5)) ?></span>
                        <?php else: ?>
                        <span class="slot-free-label">Available</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('resourceSelect')?.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('resourceName').value = opt.dataset.name || opt.text;
});
</script>
