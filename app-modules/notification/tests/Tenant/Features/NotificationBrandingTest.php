<?php

use AidingApp\Notification\Tests\Fixtures\TestEmailSettingFromNameNotification;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Support\Colors\Color as FilamentColor;

it('renders the notification mail with the configured `primary_color`', function () {
    $user = User::factory()->create();

    $expected = FilamentColor::convertToRgb(FilamentColor::all()[Color::Gray->value][600]);

    expect((string) (new TestEmailSettingFromNameNotification())->toMail($user)->render())
        ->not->toContain($expected);

    $settings = app(NotificationSettings::class);
    $settings->primary_color = Color::Gray;
    $settings->save();

    expect((string) (new TestEmailSettingFromNameNotification())->toMail($user)->render())
        ->toContain($expected);
});
