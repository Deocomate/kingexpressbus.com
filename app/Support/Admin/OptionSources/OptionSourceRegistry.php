<?php

namespace App\Support\Admin\OptionSources;

use InvalidArgumentException;

/**
 * Registry: maps resource slug → OptionSourceContract class.
 * Each module registers its own source; P4–P8 add entries here.
 */
class OptionSourceRegistry
{
    /** @var array<string, class-string<OptionSourceContract>> */
    private static array $sources = [];

    public static function register(string $slug, string $class): void
    {
        self::$sources[$slug] = $class;
    }

    /** @return string[] registered slugs */
    public static function slugs(): array
    {
        return array_keys(self::$sources);
    }

    public static function resolve(string $slug): OptionSourceContract
    {
        if (! isset(self::$sources[$slug])) {
            throw new InvalidArgumentException("Unknown option source: {$slug}");
        }
        return app(self::$sources[$slug]);
    }

    public static function has(string $slug): bool
    {
        return isset(self::$sources[$slug]);
    }
}
