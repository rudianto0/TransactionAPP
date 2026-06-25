<?php

/**
 * TransactionAPP — Front Controller / Entry Point
 * Semua request masuk lewat sini, lalu di-route ke Controller yang sesuai.
 */

declare(strict_types=1);

// Start session
session_start();

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH', BASE_PATH . '/src');
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONFIG_PATH', SRC_PATH . '/config');
define('PUBLIC_PATH', __DIR__);

// Autoload sederhana
spl_autoload_register(function (string $class) {
    $prefixes = [
        'App\\Controllers\\'  => SRC_PATH . '/Controllers/',
        'App\\Services\\'      => SRC_PATH . '/Services/',
        'App\\Models\\'        => SRC_PATH . '/Models/',
        'App\\Core\\'          => SRC_PATH . '/Core/',
        'App\\Middleware\\'    => SRC_PATH . '/Middleware/',
        'App\\Database\\'      => SRC_PATH . '/Database/',
        'App\\DTOs\\'          => SRC_PATH . '/DTOs/',
        'App\\Validators\\'    => SRC_PATH . '/Validators/',
        'App\\Helpers\\'       => SRC_PATH . '/Helpers/',
    ];

    foreach ($prefixes as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Load config
$appConfig = require CONFIG_PATH . '/app.php';
$dbConfig  = require CONFIG_PATH . '/database.php';

// Error handling
if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Exception handler
set_exception_handler(function (Throwable $e) use ($appConfig) {
    if ($appConfig['debug']) {
        http_response_code(500);
        echo '<h1>500 — Internal Server Error</h1>';
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        require VIEWS_PATH . '/errors/500.php';
    }
});

// Run the router
$router = require CONFIG_PATH . '/routes.php';
$router->dispatch();
