<?php

namespace App\Settings;

use App\Models\SettingsProperty;
use App\Models\SettingsPropertyWithMedia;
use Spatie\LaravelSettings\Settings;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;
use Spatie\LaravelSettings\SettingsRepositories\SettingsRepository;
use Spatie\MediaLibrary\InteractsWithMedia;

abstract class SettingsWithMedia extends Settings
{
    /**
     * @return class-string<SettingsPropertyWithMedia>
     */
    abstract public static function getSettingsPropertyModelClass(): string;

    public function getRepository(): SettingsRepository
    {
        return new DatabaseSettingsRepository([
            ...config('settings.repositories.database'),
            ...[
                'model' => static::getSettingsPropertyModelClass(),
            ]
        ]);
    }

    public function getSettingsPropertyModel(string $property): SettingsPropertyWithMedia
    {
        return static::getSettingsPropertyModelClass()::getInstance($property);
    }
}