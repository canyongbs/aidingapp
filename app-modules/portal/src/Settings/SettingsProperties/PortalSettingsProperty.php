<?php

namespace AidingApp\Portal\Settings\SettingsProperties;

use App\Models\SettingsPropertyWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PortalSettingsProperty extends SettingsPropertyWithMedia
{
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('portal_favicon')
            ->format('png')
            ->performOnCollections('portal_favicon')
            ->height(512)
            ->width(512);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portal_favicon')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/ico',
                'image/webp',
                'image/jpg',
                'image/svg',
            ]);

        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/*'
            ]);
    }
}
