<?php

namespace App\Features;

use Laravel\Pennant\Feature;

abstract class AbstractFeatureFlag
{
    public static function active(): bool
    {
        return Feature::active(static::class);
    }

    public static function activate(): void
    {
        Feature::activate(static::class);
    }

    public static function deactivate(): void
    {
        Feature::deactivate(static::class);
    }

    public static function purge(): void
    {
        Feature::purge(static::class);
    }
}
