<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * User Model — menangani CRUD users + relasi Many-to-Many ke roles via pivot user_roles.
 */
class User extends Model
{
    /**
     * Get all users with their roles.
     */
    public static function all(): array
    {
        $users = static::fetchAll(
            'SELECT id, name, email, is_active, last_login, created_at, updated_at
             FROM users
             ORDER BY id ASC'
        );

        // Attach roles for each user
        foreach ($users as &$user) {
            $user['roles'] = static::roles((int)$user['id']);
        }

        return $users;
    }

    /**
     * Find a user by ID (with roles).
     */
    public static function find(int $id): ?array
    {
        $user = static::fetchOne(
            'SELECT id, name, email, is_active, last_login, created_at, updated_at
             FROM users WHERE id = :id',
            ['id' => $id]
        );

        if ($user) {
            $user['roles'] = static::roles($id);
        }

        return $user;
    }

    /**
     * Find a user by email (with roles & permissions, for login).
     */
    public static function findByEmail(string $email): ?array
    {
        $user = static::fetchOne(
            'SELECT id, name, email, password, is_active, last_login, created_at, updated_at
             FROM users WHERE email = :email',
            ['email' => $email]
        );

        if ($user) {
            $user['roles'] = static::roles((int)$user['id']);
            $user['permissions'] = static::permissions((int)$user['id']);
        }

        return $user;
    }

    /**
     * Get all roles attached to a user.
     */
    public static function roles(int $userId): array
    {
        return static::fetchAll(
            'SELECT r.id, r.name, r.description
             FROM roles r
             JOIN user_roles ur ON r.id = ur.role_id
             WHERE ur.user_id = :user_id
             ORDER BY r.name ASC',
            ['user_id' => $userId]
        );
    }

    /**
     * Get all permissions (flattened across all roles) for a user.
     */
    public static function permissions(int $userId): array
    {
        $rows = static::fetchAll(
            'SELECT DISTINCT p.name
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN user_roles ur ON rp.role_id = ur.role_id
             WHERE ur.user_id = :user_id
             ORDER BY p.name ASC',
            ['user_id' => $userId]
        );

        return array_column($rows, 'name');
    }

    /**
     * Create a new user. Returns the new user ID.
     */
    public static function create(string $name, string $email, string $password, bool $isActive = true): string
    {
        return static::insert(
            'INSERT INTO users (name, email, password, is_active) VALUES (:name, :email, :password, :is_active)',
            [
                'name'      => $name,
                'email'     => $email,
                'password'  => $password,
                'is_active' => $isActive ? 1 : 0,
            ]
        );
    }

    /**
     * Update a user.
     */
    public static function update(int $id, string $name, string $email, bool $isActive): int
    {
        return static::execute(
            'UPDATE users SET name = :name, email = :email, is_active = :is_active WHERE id = :id',
            [
                'id'        => $id,
                'name'      => $name,
                'email'     => $email,
                'is_active' => $isActive ? 1 : 0,
            ]
        );
    }

    /**
     * Update user password.
     */
    public static function updatePassword(int $id, string $hashedPassword): int
    {
        return static::execute(
            'UPDATE users SET password = :password WHERE id = :id',
            ['id' => $id, 'password' => $hashedPassword]
        );
    }

    /**
     * Update last_login timestamp.
     */
    public static function updateLastLogin(int $id): void
    {
        static::execute(
            'UPDATE users SET last_login = NOW() WHERE id = :id',
            ['id' => $id]
        );
    }

    /**
     * Delete a user.
     */
    public static function delete(int $id): int
    {
        return static::execute('DELETE FROM users WHERE id = :id', ['id' => $id]);
    }

    /**
     * Attach roles to a user.
     */
    public static function attachRoles(int $userId, array $roleIds): void
    {
        $stmt = static::db()->prepare(
            'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)'
        );

        foreach ($roleIds as $roleId) {
            $stmt->execute(['user_id' => $userId, 'role_id' => (int)$roleId]);
        }
    }

    /**
     * Sync roles for a user (remove old, set new).
     */
    public static function syncRoles(int $userId, array $roleIds): void
    {
        static::execute('DELETE FROM user_roles WHERE user_id = :user_id', ['user_id' => $userId]);
        static::attachRoles($userId, $roleIds);
    }
}
