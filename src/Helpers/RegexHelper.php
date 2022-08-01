<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Helpers;

class RegexHelper
{
    public static function match(string $subject, string $pattern)
    {
        preg_match($pattern, $subject, $matches);

        if ($matches === []) {
            return '';
        }

        return $matches[1] ?? $matches[0];
    }

    public static function test(string $subject, string $pattern): bool
    {
        return static::match($subject, $pattern) !== '';
    }
}
