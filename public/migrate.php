<?php

/**
 * CLI Script: migrate.php
 * 
 * Cara pakai:
 *   php public/migrate.php
 * 
 * Script ini akan:
 *   1. Menjalankan semua SQL migration di folder migrations/
 *   2. Menjalankan AuthSeeder (roles, permissions, admin user)
 */

declare(strict_types=1);

// Definisikan base path ke root project
define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH', BASE_PATH . '/src');
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONFIG_PATH', SRC_PATH . '/config');

// Autoload sederhana (copy dari index.php)
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
        'App\\Seeders\\'       => BASE_PATH . '/seeders/',
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

echo "╔══════════════════════════════════════════╗\n";
echo "║   TransactionAPP — Migration Runner     ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

// 1. Test database connection
echo "[1/3] Testing database connection...\n";
try {
    $pdo = \App\Database\Connection::getInstance();
    echo "  ✓ Connected to MySQL successfully.\n\n";
} catch (\Exception $e) {
    echo "  ✗ Failed to connect: " . $e->getMessage() . "\n";
    echo "\n  Please check your database config in src/config/database.php\n";
    exit(1);
}

// 2. Run migrations
echo "[2/3] Running migrations...\n";
$migration = new \App\Database\Migration();
$migration->run();
echo "\n";

// 3. Run seeder
echo "[3/3] Running seeders...\n";
$seeder = new \App\Seeders\AuthSeeder();
$seeder->run();
echo "\n";

echo "═══════════════════════════════════════════\n";
echo "  ✓ All migrations & seeders completed.\n";
echo "  Admin login: admin@example.com / admin123\n";
echo "═══════════════════════════════════════════\n";
