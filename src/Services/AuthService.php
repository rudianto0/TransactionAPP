<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;

/**
 * AuthService — menangani business logic autentikasi.
 */
class AuthService
{
    /**
     * Login user dengan email dan password.
     * Returns user array on success, or null on failure.
     */
    public function login(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);

        if (!$user) {
            return null; // user not found
        }

        if (!password_verify($password, $user['password'])) {
            return null; // wrong password
        }

        if (!(bool)$user['is_active']) {
            return null; // inactive account
        }

        // Update last login
        User::updateLastLogin((int)$user['id']);

        // Store user in session (minus password)
        unset($user['password']);
        Session::regenerate();
        Session::setUser($user);

        return $user;
    }

    /**
     * Logout the current user.
     */
    public function logout(): void
    {
        Session::destroy();
    }

    /**
     * Get the currently authenticated user (from session).
     */
    public function user(): ?array
    {
        return Session::user();
    }

    /**
     * Check if user has ANY of the given roles.
     */
    public function hasRole(string $role): bool
    {
        $user = $this->user();
        if (!$user) return false;

        $roles = $user['roles'] ?? [];
        foreach ($roles as $r) {
            if ($r['name'] === $role) return true;
        }
        return false;
    }

    /**
     * Check if user has the given permission.
     * (Union of all permissions from all roles).
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->user();
        if (!$user) return false;

        $permissions = $user['permissions'] ?? [];
        return in_array($permission, $permissions, true);
    }
}
