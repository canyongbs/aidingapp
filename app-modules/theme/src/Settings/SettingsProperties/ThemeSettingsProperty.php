<?php

namespace AidingApp\Theme\Settings\SettingsProperties;

use App\Models\SettingsPropertyWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ThemeSettingsProperty extends SettingsPropertyWithMedia
{
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('logo-height-250px')
            ->performOnCollections('logo', 'dark_logo')
            ->height(250)
            ->keepOriginalImageFormat();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/*'
            ]);

        $this->addMediaCollection('dark_logo')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/*'
            ]);
    }
}
