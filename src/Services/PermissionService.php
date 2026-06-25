<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;

/**
 * PermissionService — business logic untuk manajemen permission.
 */
class PermissionService
{
    /**
     * Get all permissions.
     */
    public function all(): array
    {
        return Permission::all();
    }

    /**
     * Get all permission names as flat array.
     */
    public function allNames(): array
    {
        return Permission::allNames();
    }
}
