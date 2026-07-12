<?php
/**
 * AssetFlow - Helper Functions
 */

/**
 * Escape output for safe HTML display.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a page within the application.
 */
function redirect(string $page, array $params = []): void
{
    $query = http_build_query(array_merge(['page' => $page], $params));
    header('Location: index.php?' . $query);
    exit;
}

/**
 * Check if user is logged in.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Store authenticated user in session.
 */
function loginUser(array $user): void
{
    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name']  = $user['full_name'] ?? '';
    $_SESSION['user_role']  = $user['role'] ?? 'employee';
}

/**
 * Set a flash message for the next request.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message.
 */
function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

/**
 * Validate email format.
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Require authenticated user; redirect to login if not signed in.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please sign in to continue.');
        redirect('login');
    }
}

/**
 * Get current logged-in user details from session.
 */
function currentUser(): array
{
    return [
        'id'    => $_SESSION['user_id'] ?? null,
        'name'  => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role'] ?? 'employee',
    ];
}

/**
 * Require specific user role(s).
 */
function requireRole(string ...$roles): void
{
    requireAuth();

    if (!in_array(currentUser()['role'], $roles, true)) {
        setFlash('error', 'You do not have permission to access this page.');
        redirect('dashboard');
    }
}

/**
 * Log activity to the activity feed.
 */
function logActivity(string $type, string $message, string $icon = 'bi-circle-fill', ?string $badge = null): void
{
    try {
        $stmt = db()->prepare(
            'INSERT INTO activity_log (activity_type, message, icon, badge) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$type, $message, $icon, $badge]);
    } catch (PDOException $e) {
        // Silently fail if table unavailable
    }
}

/**
 * Format datetime as relative time (e.g. "2m ago").
 */
function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);

    if ($diff < 60) {
        return 'Just now';
    }
    if ($diff < 3600) {
        return (int) floor($diff / 60) . 'm ago';
    }
    if ($diff < 86400) {
        return (int) floor($diff / 3600) . 'h ago';
    }
    if ($diff < 604800) {
        return (int) floor($diff / 86400) . 'd ago';
    }

    return date('M j, Y', strtotime($datetime));
}

/**
 * Render asset status badge HTML.
 */
function assetStatusBadge(string $status): string
{
    $map = [
        'available'   => ['Available',   'badge-available'],
        'allocated'   => ['Allocated',   'badge-allocated'],
        'maintenance' => ['Maintenance', 'badge-maintenance'],
        'inactive'    => ['Inactive',    'badge-inactive'],
    ];

    [$label, $class] = $map[$status] ?? ['Unknown', 'badge-inactive'];

    return '<span class="status-badge ' . e($class) . '">' . e($label) . '</span>';
}

/**
 * Render verification status badge for audits.
 */
function verificationBadge(string $status): string
{
    $map = [
        'verified' => ['Verified', 'badge-verified'],
        'missing'  => ['Missing',  'badge-missing'],
        'damaged'  => ['Damaged',  'badge-damaged'],
        'pending'  => ['Pending',  'badge-pending'],
    ];

    [$label, $class] = $map[$status] ?? ['Unknown', 'badge-pending'];

    return '<span class="status-badge ' . e($class) . '">' . e($label) . '</span>';
}

/**
 * Render a view inside the main application layout.
 */
function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    require APP_ROOT . '/views/' . $view . '.php';
    $content = ob_get_clean();

    require APP_ROOT . '/views/layouts/app.php';
}

/**
 * Get sidebar navigation items.
 */
function getNavItems(): array
{
    return [
        ['page' => 'dashboard',            'icon' => 'bi-speedometer2',    'label' => 'Dashboard'],
        ['page' => 'organization-setup',   'icon' => 'bi-building',        'label' => 'Organization Setup'],
        ['page' => 'assets',               'icon' => 'bi-box-seam',        'label' => 'Assets'],
        ['page' => 'allocation-transfer',  'icon' => 'bi-arrow-left-right','label' => 'Allocation & Transfer'],
        ['page' => 'resource-booking',     'icon' => 'bi-calendar-check',  'label' => 'Resource Booking'],
        ['page' => 'maintenance',          'icon' => 'bi-tools',           'label' => 'Maintenance'],
        ['page' => 'audit',                'icon' => 'bi-shield-check',    'label' => 'Audit'],
        ['page' => 'reports',              'icon' => 'bi-bar-chart-line',  'label' => 'Reports'],
        ['page' => 'notifications',        'icon' => 'bi-bell',            'label' => 'Notifications'],
    ];
}
