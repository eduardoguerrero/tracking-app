<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

/**
 * OWASP: XSS Prevention
 * Sanitizes user-provided strings by stripping HTML/script tags and encoding entities.
 * Applied as defense in depth — the API is JSON-only, but if any value is later
 * rendered in HTML context, it is already safe.
 */
class XssSanitizer
{
    /**
     * Strips all HTML/script tags and encodes special characters.
     */
    public static function sanitize(string $value): string
    {
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        return $value;
    }

    /**
     * Strips all HTML/script tags only (preserves text).
     */
    public static function stripTags(string $value): string
    {
        return strip_tags($value);
    }
}
