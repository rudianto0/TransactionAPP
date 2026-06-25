<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session management wrapper.
 */
class Session
{
    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Set the currently authenticated user.
     */
    public static function setUser(array $user): void
    {
        static::set('user', $user);
    }

    /**
     * Get the currently authenticated user, or null.
     */
    public static function user(): ?array
    {
        return static::get('user');
    }

    /**
     * Check if a user is logged in.
     */
    public static function isLoggedIn(): bool
    {
        return static::has('user');
    }

    /**
     * Regenerate session ID (anti session fixation).
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Destroy session completely (logout).
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }
}
