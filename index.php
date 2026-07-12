<?php
/**
 * AssetFlow - Front Controller
 * Routes all requests to the appropriate controller action.
 */

require_once __DIR__ . '/includes/bootstrap.php';

// Allowed routes (whitelist for security)
$routes = [
    'login'                => ['controller' => 'AuthController',      'action' => 'login',           'public' => true],
    'signup'               => ['controller' => 'AuthController',      'action' => 'signup',          'public' => true],
    'forgot-password'      => ['controller' => 'AuthController',      'action' => 'forgotPassword',  'public' => true],
    'logout'               => ['controller' => 'AuthController',      'action' => 'logout',          'public' => false],
    'dashboard'            => ['controller' => 'DashboardController',   'action' => 'index',           'public' => false],
    'organization-setup'   => ['controller' => 'OrganizationController', 'action' => 'index',           'public' => false],
    'assets'               => ['controller' => 'AssetController',        'action' => 'index',           'public' => false],
    'allocation-transfer'  => ['controller' => 'AllocationController',   'action' => 'index',           'public' => false],
    'resource-booking'     => ['controller' => 'BookingController',      'action' => 'index',           'public' => false],
    'maintenance'          => ['controller' => 'MaintenanceController',  'action' => 'index',           'public' => false],
    'audit'                => ['controller' => 'AuditController',        'action' => 'index',           'public' => false],
    'reports'              => ['controller' => 'ReportController',       'action' => 'index',           'public' => false],
    'notifications'        => ['controller' => 'NotificationController', 'action' => 'index',           'public' => false],
];

// Get requested page (default to login)
$page = $_GET['page'] ?? 'login';

// Redirect to login if route not found
if (!isset($routes[$page])) {
    redirect('login');
}

$route      = $routes[$page];
$controller = $route['controller'];
$action     = $route['action'];
$isPublic   = $route['public'] ?? false;

// Require authentication for protected routes
if (!$isPublic && !isLoggedIn()) {
    setFlash('error', 'Please sign in to continue.');
    redirect('login');
}

// Handle pages not yet implemented
if ($controller === null || $action === null) {
    setFlash('info', 'This page is coming soon.');
    redirect('dashboard');
}

// Check controller exists
if (!class_exists($controller)) {
    if ($page === 'login') {
        die('Application error: AuthController not found.');
    }
    setFlash('info', 'This page is coming soon.');
    redirect(isLoggedIn() ? 'dashboard' : 'login');
}

$instance = new $controller();

// Check action method exists
if (!method_exists($instance, $action)) {
    setFlash('info', 'This page is coming soon.');
    redirect(isLoggedIn() ? 'dashboard' : 'login');
}

// Dispatch to controller action
$instance->$action();
