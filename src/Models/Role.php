<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Role Model — menangani operasi CRUD pada tabel roles.
 */
class Role extends Model
{
    /**
     * Get all roles.
     */
    public static function all(): array
    {
        return static::fetchAll('SELECT id, name, description, created_at FROM roles ORDER BY id ASC');
    }

    /**
     * Find a role by ID.
     */
    public static function find(int $id): ?array
    {
        return static::fetchOne('SELECT id, name, description, created_at FROM roles WHERE id = :id', ['id' => $id]);
    }

    /**
     * Find a role by name.
     */
    public static function findByName(string $name): ?array
    {
        return static::fetchOne('SELECT id, name, description, created_at FROM roles WHERE name = :name', ['name' => $name]);
    }

    /**
     * Get all permissions attached to a role.
     */
    public static function permissions(int $roleId): array
    {
        return static::fetchAll(
            'SELECT p.id, p.name, p.description
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = :role_id
             ORDER BY p.name ASC',
            ['role_id' => $roleId]
        );
    }

    /**
     * Create a new role, return its ID.
     */
    public static function create(string $name, ?string $description = null): string
    {
        return static::insert(
            'INSERT INTO roles (name, description) VALUES (:name, :description)',
            ['name' => $name, 'description' => $description]
        );
    }

    /**
     * Update a role.
     */
    public static function update(int $id, string $name, ?string $description = null): int
    {
        return static::execute(
            'UPDATE roles SET name = :name, description = :description WHERE id = :id',
            ['id' => $id, 'name' => $name, 'description' => $description]
        );
    }

    /**
     * Delete a role.
     */
    public static function delete(int $id): int
    {
        return static::execute('DELETE FROM roles WHERE id = :id', ['id' => $id]);
    }
}
