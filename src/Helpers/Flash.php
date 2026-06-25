<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Flash message helper — menyimpan pesan singkat di session (1 kali pakai).
 */
class Flash
{
    private const SESSION_KEY = '_flash';

    /**
     * Set a flash message.
     */
    public static function set(string $key, string $message): void
    {
        $_SESSION[self::SESSION_KEY][$key] = $message;
    }

    /**
     * Get and clear a flash message.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        if (isset($_SESSION[self::SESSION_KEY][$key])) {
            $message = $_SESSION[self::SESSION_KEY][$key];
            unset($_SESSION[self::SESSION_KEY][$key]);
            return $message;
        }
        return $default;
    }

    /**
     * Set a success message.
     */
    public static function success(string $message): void
    {
        static::set('success', $message);
    }

    /**
     * Set an error message.
     */
    public static function error(string $message): void
    {
        static::set('error', $message);
    }
}
