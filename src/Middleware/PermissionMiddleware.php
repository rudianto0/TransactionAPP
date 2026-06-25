<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;

/**
 * PermissionMiddleware — cek apakah user memiliki permission tertentu.
 * 
 * Penggunaan di route:
 *   PermissionMiddleware::class . ':manage-users'
 *   
 * Constructor menerima parameter permission name.
 */
class PermissionMiddleware
{
    private string $permission;

    public function __construct(string $permission = '')
    {
        $this->permission = $permission;
    }

    /**
     * Handle the middleware.
     * Dipanggil oleh Router. Router akan memecah string "Class:param".
     */
    public function handle(array $params = []): void
    {
        $user = Session::user();

        if (!$user) {
            http_response_code(403);
            echo '<h1>403 — Forbidden</h1><p>Anda tidak memiliki akses ke halaman ini.</p>';
            exit;
        }

        $permissions = $user['permissions'] ?? [];

        if (!in_array($this->permission, $permissions, true)) {
            http_response_code(403);
            echo '<h1>403 — Forbidden</h1><p>Anda tidak memiliki permission: <strong>' . htmlspecialchars($this->permission) . '</strong></p>';
            exit;
        }
    }
}
