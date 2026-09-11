<?php

use App\Features\NotificationSettingsFeature;
use App\Models\NotificationSetting;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            if (Schema::hasTable('notification_settings')) {
                $notificationSetting = NotificationSetting::query()->oldest()->first();

                if ($notificationSetting) {
                    $settings = app(NotificationSettings::class);

                    $settings->from_name = $notificationSetting->getAttribute('from_name');
                    $settings->primary_color = Color::tryFrom((string) $notificationSetting->getAttribute('primary_color'));

                    $settings->save();

                    $logo = $notificationSetting->getFirstMedia('logo');

                    $logo?->copy(NotificationSettings::getSettingsPropertyModel('notifications.logo'), 'logo', 's3-public');
                }
            }

            NotificationSettingsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            NotificationSettingsFeature::deactivate();

            $settings = app(NotificationSettings::class);

            $settings->from_name = null;
            $settings->primary_color = null;

            $settings->save();

            NotificationSettings::getSettingsPropertyModel('notifications.logo')->clearMediaCollection('logo');
        });
    }
};
