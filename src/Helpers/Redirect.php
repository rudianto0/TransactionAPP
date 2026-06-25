<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * URL Redirect helper.
 */
class Redirect
{
    /**
     * Redirect to a path (relative to BASE_URL).
     */
    public static function to(string $path): void
    {
        $config = require CONFIG_PATH . '/app.php';
        $url = rtrim($config['url'], '/') . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect back with a flash message.
     */
    public static function back(): void
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referrer);
        exit;
    }
}
