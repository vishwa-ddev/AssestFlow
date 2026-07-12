<?php
/**
 * AssetFlow - Application Bootstrap
 * Loads configuration, starts session, and registers autoloading.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Start session with secure defaults
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Simple autoloader for models and controllers
spl_autoload_register(function (string $class): void {
    $paths = [
        APP_ROOT . '/models/' . $class . '.php',
        APP_ROOT . '/controllers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
