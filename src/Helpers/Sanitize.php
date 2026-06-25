<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Input sanitization helper.
 */
class Sanitize
{
    /**
     * Sanitize a string input.
     */
    public static function string(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize an email input.
     */
    public static function email(string $value): string
    {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Escape HTML output.
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
