<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;

/**
 * RoleService — business logic untuk manajemen role.
 */
class RoleService
{
    /**
     * Get all roles.
     */
    public function all(): array
    {
        return Role::all();
    }

    /**
     * Get all roles with their permissions.
     */
    public function allWithPermissions(): array
    {
        $roles = Role::all();
        foreach ($roles as &$role) {
            $role['permissions'] = Role::permissions((int)$role['id']);
        }
        return $roles;
    }
}
