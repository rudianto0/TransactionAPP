<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Permission Model — menangani operasi pada tabel permissions.
 */
class Permission extends Model
{
    /**
     * Get all permissions.
     */
    public static function all(): array
    {
        return static::fetchAll('SELECT id, name, description, created_at FROM permissions ORDER BY id ASC');
    }

    /**
     * Find a permission by ID.
     */
    public static function find(int $id): ?array
    {
        return static::fetchOne('SELECT id, name, description, created_at FROM permissions WHERE id = :id', ['id' => $id]);
    }

    /**
     * Find a permission by name.
     */
    public static function findByName(string $name): ?array
    {
        return static::fetchOne('SELECT id, name, description, created_at FROM permissions WHERE name = :name', ['name' => $name]);
    }

    /**
     * Get all permission names as flat array.
     */
    public static function allNames(): array
    {
        $rows = static::fetchAll('SELECT name FROM permissions ORDER BY name ASC');
        return array_column($rows, 'name');
    }
}
