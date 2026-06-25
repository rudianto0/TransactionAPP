<?php

declare(strict_types=1);

namespace App\Seeders;

use PDO;

/**
 * Auth Seeder — Menyediakan data awal untuk auth system.
 *
 * Yang di-seed:
 *   1. Default Roles: Admin, User
 *   2. Default Permissions: manage-users, manage-roles, manage-transactions, manage-categories, view-reports, export-data
 *   3. Mapping Role ↔ Permission (Admin = ALL, User = sebagian)
 *   4. Default Admin User: admin@example.com / admin123
 *   5. Mapping User ↔ Role (admin → Admin)
 */
class AuthSeeder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = \App\Database\Connection::getInstance();
    }

    /**
     * Run all seeders in order.
     */
    public function run(): void
    {
        echo "  Running AuthSeeder...\n";

        $this->seedRoles();
        $this->seedPermissions();
        $this->seedRolePermissions();
        $this->seedAdminUser();

        echo "  AuthSeeder complete.\n";
    }

    /**
     * Seed default roles.
     */
    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Administrator — full access to all features'],
            ['name' => 'User',  'description' => 'Regular user — basic permissions'],
        ];

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO roles (name, description) VALUES (:name, :description)'
        );

        foreach ($roles as $role) {
            $stmt->execute($role);
        }

        echo "    - Roles seeded.\n";
    }

    /**
     * Seed default permissions.
     */
    private function seedPermissions(): void
    {
        $permissions = [
            ['name' => 'manage-users',        'description' => 'Manage users (CRUD)'],
            ['name' => 'manage-roles',         'description' => 'Manage roles & permissions'],
            ['name' => 'manage-transactions',  'description' => 'Manage transactions'],
            ['name' => 'manage-categories',    'description' => 'Manage categories'],
            ['name' => 'view-reports',         'description' => 'View reports'],
            ['name' => 'export-data',          'description' => 'Export PDF/Excel'],
        ];

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO permissions (name, description) VALUES (:name, :description)'
        );

        foreach ($permissions as $perm) {
            $stmt->execute($perm);
        }

        echo "    - Permissions seeded.\n";
    }

    /**
     * Seed role ↔ permission mapping.
     * Admin gets ALL permissions. User gets basic ones.
     */
    private function seedRolePermissions(): void
    {
        // Get role IDs
        $adminRoleId = $this->getRoleId('Admin');
        $userRoleId  = $this->getRoleId('User');

        // Get all permission IDs
        $allPermissions = $this->pdo->query('SELECT id, name FROM permissions')->fetchAll(PDO::FETCH_ASSOC);

        // Admin: ALL permissions (loop semua permission yang ada)
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
        );

        foreach ($allPermissions as $perm) {
            $stmt->execute([
                'role_id'       => $adminRoleId,
                'permission_id' => $perm['id'],
            ]);
        }
        echo "    - Admin role → ALL permissions (" . count($allPermissions) . " assigned).\n";

        // User: basic permissions
        $userPermissions = ['manage-transactions', 'manage-categories', 'view-reports'];
        foreach ($userPermissions as $permName) {
            $permId = $this->getPermissionId($permName);
            if ($permId) {
                $stmt->execute([
                    'role_id'       => $userRoleId,
                    'permission_id' => $permId,
                ]);
            }
        }
        echo "    - User role → " . count($userPermissions) . " permissions assigned.\n";
    }

    /**
     * Seed default admin user and assign Admin role.
     */
    private function seedAdminUser(): void
    {
        // Check if admin user already exists
        $existing = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
        $existing->execute(['email' => 'admin@example.com']);

        if ($existing->fetch()) {
            echo "    - Admin user already exists, skipping.\n";
            return;
        }

        // Insert admin user with bcrypt hashed password
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, is_active) VALUES (:name, :email, :password, :is_active)'
        );
        $stmt->execute([
            'name'      => 'Admin',
            'email'     => 'admin@example.com',
            'password'  => password_hash('admin123', PASSWORD_BCRYPT),
            'is_active' => 1,
        ]);

        $userId = $this->pdo->lastInsertId();

        // Assign Admin role
        $adminRoleId = $this->getRoleId('Admin');
        $roleStmt = $this->pdo->prepare(
            'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)'
        );
        $roleStmt->execute([
            'user_id' => $userId,
            'role_id' => $adminRoleId,
        ]);

        echo "    - Admin user created: admin@example.com / admin123\n";
    }

    /**
     * Get role ID by name.
     */
    private function getRoleId(string $name): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM roles WHERE name = :name');
        $stmt->execute(['name' => $name]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get permission ID by name.
     */
    private function getPermissionId(string $name): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM permissions WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }
}
