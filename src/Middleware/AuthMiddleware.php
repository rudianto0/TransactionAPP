<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;

/**
 * AuthMiddleware — memastikan user sudah login sebelum akses route.
 * Jika belum login, redirect ke halaman login.
 */
class AuthMiddleware
{
    public function handle(array $params = []): void
    {
        if (!Session::isLoggedIn()) {
            $config = require CONFIG_PATH . '/app.php';
            header('Location: ' . $config['url'] . '/login');
            exit;
        }
    }
}
