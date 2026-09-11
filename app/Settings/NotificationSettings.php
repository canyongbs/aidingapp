<?php

namespace App\Settings;

use App\Settings\SettingsProperties\NotificationSettingsProperty;
use CanyonGBS\Common\Enums\Color;

class NotificationSettings extends SettingsWithMedia
{
    public ?string $from_name = null;

    public null $logo = null;

    public ?Color $primary_color = null;

    public static function getSettingsPropertyModelClass(): string
    {
        return NotificationSettingsProperty::class;
    }

    public static function group(): string
    {
        return 'notifications';
    }
}
