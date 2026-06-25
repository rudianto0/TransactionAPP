<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request wrapper.
 */
class Request
{
    /**
     * Get a value from GET or use default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }

    /**
     * Get a value from POST or use default.
     */
    public static function post(string $key, ?string $default = null): ?string
    {
        return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }

    /**
     * Get all POST data.
     */
    public static function all(): array
    {
        return $_POST;
    }

    /**
     * Get the current HTTP method.
     */
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Check if the request is a POST request.
     */
    public static function isPost(): bool
    {
        return static::method() === 'POST';
    }
}
