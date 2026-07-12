<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="data-card">
    <div class="data-card-header">
        <h2 class="data-card-title">Register New Asset</h2>
        <a href="index.php?page=assets" class="btn btn-outline-secondary btn-sm">Back to Directory</a>
    </div>

    <form method="post" action="index.php?page=assets&action=store" enctype="multipart/form-data" class="register-form">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Asset Tag <span class="text-danger">*</span></label>
                <input type="text" name="asset_code" class="form-control" placeholder="AF0012" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= e((string) $cat['id']) ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">Select department</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?= e((string) $dept['id']) ?>"><?= e($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select">
                    <option value="">Select vendor</option>
                    <?php foreach ($vendors as $vendor): ?>
                    <option value="<?= e((string) $vendor['id']) ?>"><?= e($vendor['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Warranty Until</label>
                <input type="date" name="warranty_until" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Current Location</label>
                <input type="text" name="location" class="form-control" placeholder="Bengaluru">
            </div>
            <div class="col-md-6">
                <label class="form-label">Condition</label>
                <select name="condition_note" class="form-select">
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Poor">Poor</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">QR Code</label>
                <input type="text" name="qr_code" class="form-control" placeholder="Auto-generated if empty">
                <div class="qr-placeholder mt-2">
                    <i class="bi bi-qr-code"></i>
                    <span>QR preview placeholder</span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="available">Available</option>
                    <option value="allocated">Allocated</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Register Asset
            </button>
            <a href="index.php?page=assets" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
