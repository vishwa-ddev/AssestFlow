<?php
/**
 * AssetFlow - Database Installer
 * Creates the database and tables from schema.sql.
 *
 * Usage: http://localhost/AssestFlow/database/install.php
 * Remove or protect this file in production.
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'assetflow';
$schemaFile = __DIR__ . '/schema.sql';

if (!file_exists($schemaFile)) {
    die('Schema file not found.');
}

/**
 * Seed all demo data for AssetFlow modules.
 */
function seedDashboardData(PDO $pdo): void
{
    $assetCount = (int) $pdo->query('SELECT COUNT(*) FROM assets')->fetchColumn();
    if ($assetCount > 0) {
        return;
    }

    // Master data
    $pdo->exec("INSERT INTO departments (name) VALUES ('Engineering'), ('Operations'), ('HR'), ('Procurement')");
    $pdo->exec("INSERT INTO asset_categories (name) VALUES ('Electronics'), ('Furniture'), ('Vehicles'), ('Office Equipment')");
    $pdo->exec("INSERT INTO vendors (name) VALUES ('Dell India'), ('Epson'), ('IKEA'), ('Toyota Material Handling')");

    $depts = $pdo->query('SELECT id, name FROM departments')->fetchAll(PDO::FETCH_KEY_PAIR);
    $cats  = $pdo->query('SELECT id, name FROM asset_categories')->fetchAll(PDO::FETCH_KEY_PAIR);
    $vendors = $pdo->query('SELECT id, name FROM vendors')->fetchAll(PDO::FETCH_KEY_PAIR);

    $electronics = array_search('Electronics', $cats);
    $furniture   = array_search('Furniture', $cats);
    $vehicles    = array_search('Vehicles', $cats);
    $engineering = array_search('Engineering', $depts);

    // Featured demo assets from spec
    $featured = [
        ['AF0012', 'Dell Laptop',  'Electronics', 'allocated',   'Bengaluru',   'SN-DL-0012', $electronics, $engineering],
        ['AF0062', 'Projector',    'Electronics', 'maintenance', 'HQ Floor 2',  'SN-PJ-0062', $electronics, $engineering],
        ['AF0201', 'Office Chair', 'Furniture',   'available',   'Warehouse',   'SN-CH-0201', $furniture, null],
        ['AF0014', 'Dell Laptop',  'Electronics', 'allocated',   'Desk E12',    'SN-DL-0014', $electronics, $engineering],
        ['AF0003', 'AC Unit',      'Electronics', 'maintenance', 'Floor 3',     'SN-AC-0003', $electronics, $engineering],
        ['AF0078', 'Forklift',     'Vehicles',    'maintenance', 'Warehouse',   'SN-FL-0078', $vehicles, null],
        ['AF0897', 'Printer',      'Office Equipment', 'maintenance', 'Print Room', 'SN-PR-0897', $electronics, null],
        ['AF0873', 'Chair',        'Furniture',   'available',   'HR Floor',    'SN-CH-0873', $furniture, null],
        ['AF0021', 'Office Chair', 'Furniture',   'allocated',   'Desk E14',    'SN-CH-0021', $furniture, $engineering],
        ['AF9838', 'Monitor',      'Electronics', 'allocated',   'Desk E15',    'SN-MN-9838', $electronics, $engineering],
    ];

    $assetStmt = $pdo->prepare(
        'INSERT INTO assets (asset_code, name, asset_type, serial_number, category_id, department_id,
         vendor_id, purchase_date, warranty_until, location, condition_note, qr_code, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $assetIds = [];
    foreach ($featured as $i => $a) {
        $vendorId = $i % 4 + 1;
        $assetStmt->execute([
            $a[0], $a[1], $a[2], $a[5], $a[6], $a[7],
            $vendorId,
            date('Y-m-d', strtotime('-' . ($i + 6) . ' months')),
            date('Y-m-d', strtotime('+' . ($i + 1) . ' years')),
            $a[4], 'Good', 'QR-' . $a[0], $a[3],
        ]);
        $assetIds[$a[0]] = (int) $pdo->lastInsertId();
    }

    // Bulk assets for dashboard KPIs
    $statusCounts = ['available' => 115, 'allocated' => 68, 'maintenance' => 5];
    $counter = 100;
    foreach ($statusCounts as $status => $count) {
        for ($i = 0; $i < $count; $i++) {
            $code = 'AF' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT);
            $types = ['Laptop', 'Monitor', 'Projector', 'Phone', 'Tablet'];
            $type  = $types[$counter % count($types)];
            $assetStmt->execute([
                $code, $type . ' Unit ' . $counter, $type, 'SN-' . $counter,
                $electronics, $engineering, 1,
                date('Y-m-d', strtotime('-1 year')),
                date('Y-m-d', strtotime('+2 years')),
                'Office', 'Good', 'QR-' . $code, $status,
            ]);
            $counter++;
        }
    }

    // Allocations
    $allocStmt = $pdo->prepare(
        'INSERT INTO asset_allocations (asset_id, assigned_to, department, status, expected_return_date) VALUES (?, ?, ?, ?, ?)'
    );
    $allocStmt->execute([$assetIds['AF0014'], 'Priya Shah', 'Engineering', 'active', date('Y-m-d', strtotime('+30 days'))]);
    $allocStmt->execute([$assetIds['AF0012'], 'Rahul Mehta', 'Engineering', 'active', date('Y-m-d', strtotime('+14 days'))]);

    // Overdue allocations
    $overdueAssets = $pdo->query("SELECT id FROM assets WHERE status = 'allocated' LIMIT 3 OFFSET 2")->fetchAll(PDO::FETCH_COLUMN);
    $assignees = ['Priya Shah', 'Rahul Mehta', 'Anita Desai'];
    foreach ($overdueAssets as $idx => $assetId) {
        $allocStmt->execute([$assetId, $assignees[$idx], 'Engineering', 'active', date('Y-m-d', strtotime('-' . ($idx + 2) . ' days'))]);
    }

    // Allocation history
    $histStmt = $pdo->prepare(
        'INSERT INTO allocation_history (asset_id, event_type, employee_name, department, condition_note, created_at) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $histStmt->execute([$assetIds['AF0014'], 'allocated', 'Priya Shah', 'Engineering', null, date('Y-m-d', strtotime('Mar 12'))]);
    $histStmt->execute([$assetIds['AF0014'], 'returned', 'Arjun Nair', null, 'Good', date('Y-m-d', strtotime('Jan 04'))]);

    // Resources & bookings
    $pdo->exec("INSERT INTO resources (name) VALUES ('Conference Room B2'), ('Conference Hall A'), ('Lab 3'), ('Meeting Pod 1')");
    $resourceId = (int) $pdo->query("SELECT id FROM resources WHERE name = 'Conference Room B2'")->fetchColumn();

    $bookingStmt = $pdo->prepare(
        'INSERT INTO bookings (resource_id, resource_name, booked_by, status, booking_date, start_time, end_time, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $bookingStmt->execute([$resourceId, 'Conference Room B2', 'Procurement Team', 'active', date('Y-m-d'), '09:00:00', '10:00:00', date('Y-m-d'), date('Y-m-d')]);

    for ($i = 0; $i < 15; $i++) {
        $resources = ['Conference Hall A', 'Lab 3', 'Meeting Pod 1'];
        $bookingStmt->execute([
            null, $resources[$i % 3], 'Team Member ' . ($i + 1), 'active',
            date('Y-m-d'), '10:00:00', '11:00:00', date('Y-m-d'), date('Y-m-d', strtotime('+3 days')),
        ]);
    }

    // Transfers
    $transferStmt = $pdo->prepare('INSERT INTO transfers (asset_id, from_user, to_user, reason, status) VALUES (?, ?, ?, ?, ?)');
    $transferStmt->execute([$assetIds['AF0014'], 'Priya Shah', 'Arjun Nair', 'Department change', 'pending']);
    $availAssets = $pdo->query("SELECT id FROM assets WHERE status = 'available' LIMIT 4")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($availAssets as $idx => $aid) {
        $transferStmt->execute([$aid, 'User A' . $idx, 'User B' . $idx, 'Reallocation request', 'pending']);
    }

    // Maintenance kanban
    $maintStmt = $pdo->prepare(
        'INSERT INTO maintenance_requests (asset_id, title, issue_description, stage, technician_name, resolved_at) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $maintStmt->execute([$assetIds['AF0062'], 'Projector Bulb', 'Not Turning On', 'pending', null, null]);
    $maintStmt->execute([$assetIds['AF0003'], 'AC Unit', 'Noisy Compressor', 'pending', null, null]);
    $maintStmt->execute([$assetIds['AF0078'], 'Forklift', 'Hydraulic leak', 'technician_assigned', 'R Varma', null]);
    $maintStmt->execute([$assetIds['AF0897'], 'Printer Jam', 'Parts Ordered', 'in_progress', null, null]);
    $maintStmt->execute([$assetIds['AF0873'], 'Chair Repair', 'Wheel replacement', 'resolved', null, '2026-07-07']);

    // Audit cycle
    $pdo->exec(
        "INSERT INTO audit_cycles (title, department, start_date, end_date, status)
         VALUES ('Q3 Audit', 'Engineering Department', '2026-07-01', '2026-07-15', 'open')"
    );
    $auditId = (int) $pdo->lastInsertId();
    $pdo->exec("INSERT INTO audit_auditors (audit_id, auditor_name) VALUES ({$auditId}, 'A Rao'), ({$auditId}, 'S Iqbal')");

    $checkStmt = $pdo->prepare(
        'INSERT INTO audit_checklist (audit_id, asset_id, expected_location, verification) VALUES (?, ?, ?, ?)'
    );
    $checkStmt->execute([$auditId, $assetIds['AF0014'], 'Desk E12', 'verified']);
    $checkStmt->execute([$auditId, $assetIds['AF0021'], 'Desk E14', 'missing']);
    $checkStmt->execute([$auditId, $assetIds['AF9838'], 'Desk E15', 'damaged']);

    // Activity log
    $activityStmt = $pdo->prepare(
        'INSERT INTO activity_log (activity_type, message, icon, badge, created_at) VALUES (?, ?, ?, ?, ?)'
    );
    $activities = [
        ['allocation', 'Laptop AF0014 assigned to Priya Shah',       'bi-laptop',             'Alert',    '-2 minutes'],
        ['approval',   'Maintenance request approved',               'bi-check-circle',       'Approval', '-18 minutes'],
        ['booking',    'Room B2 booking confirmed',                  'bi-door-open',          'Booking',  '-1 hour'],
        ['transfer',   'Transfer approved',                          'bi-arrow-left-right',   'Approval', '-3 hours'],
        ['alert',      'Overdue return',                             'bi-exclamation-triangle','Alert',   '-1 day'],
        ['audit',      'Audit discrepancy detected',                 'bi-shield-exclamation', 'Alert',    '-2 days'],
        ['allocation', 'Laptop AF-0114 allocated to Priya Shah',     'bi-laptop',             null,       '-2 hours'],
        ['booking',    'Room B2 booking confirmed',                  'bi-door-open',          null,       '-4 hours'],
        ['maintenance','Projector AF-0062 maintenance completed',      'bi-easel2',             null,       '-6 hours'],
    ];
    foreach ($activities as $a) {
        $activityStmt->execute([$a[0], $a[1], $a[2], $a[3], date('Y-m-d H:i:s', strtotime($a[4]))]);
    }
}

try {
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec(
        "CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
    $pdo->exec("USE {$dbName}");

    $sql = file_get_contents($schemaFile);
    $lines = explode("\n", $sql);
    $cleanSql = '';
    foreach ($lines as $line) {
        $trimmed = ltrim($line);
        if ($trimmed === '' || strpos($trimmed, '--') === 0) {
            continue;
        }
        $cleanSql .= $line . "\n";
    }

    $statements = array_filter(
        array_map('trim', explode(';', $cleanSql)),
        fn($stmt) => $stmt !== ''
    );

    foreach ($statements as $statement) {
        if (preg_match('/^\s*(CREATE\s+DATABASE|USE)\s/i', $statement)) {
            continue;
        }
        $pdo->exec($statement);
    }

    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $seedStmt = $pdo->prepare(
        'INSERT IGNORE INTO users (email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?)'
    );
    $seedStmt->execute(['admin@assetflow.com', $hashedPassword, 'System Administrator', 'admin', 'active']);
    $seedStmt->execute(['employee@assetflow.com', $hashedPassword, 'Demo Employee', 'employee', 'active']);

    seedDashboardData($pdo);

    $uploadsDir = dirname(__DIR__) . '/uploads/assets';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    echo '<h2>AssetFlow database installed successfully.</h2>';
    echo '<p>Demo accounts:</p>';
    echo '<ul>';
    echo '<li>admin@assetflow.com / admin123</li>';
    echo '<li>employee@assetflow.com / admin123</li>';
    echo '</ul>';
    echo '<p><a href="../index.php?page=login">Go to Login</a> | ';
    echo '<a href="../index.php?page=dashboard">Go to Dashboard</a> | ';
    echo '<a href="../index.php?page=assets">Asset Directory</a></p>';

} catch (PDOException $e) {
    die('Installation failed: ' . $e->getMessage());
}
