<?php

namespace App\Settings\SettingsProperties;

use App\Models\SettingsPropertyWithMedia;
use Override;

/**
 * @mixin IdeHelperNotificationSettingsProperty
 */
class NotificationSettingsProperty extends SettingsPropertyWithMedia
{
    #[Override]
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }
}
