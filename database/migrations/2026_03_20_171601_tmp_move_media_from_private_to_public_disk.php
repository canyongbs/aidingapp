<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Theme\Settings\ThemeSettings;
use App\Features\MediaToPublicDiskFeature;
use App\Models\NotificationSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            // Move theme logo and dark_logo from private s3 to public s3-public disk
            $themeModel = ThemeSettings::getSettingsPropertyModel('theme.is_logo_active');

            Media::query()
                ->where('model_type', 'theme_settings_property')
                ->where('model_id', $themeModel->getKey())
                ->whereIn('collection_name', ['logo', 'dark_logo'])
                ->where('disk', 's3')
                ->each(function (Media $media) use ($themeModel) {
                    $media->move($themeModel, $media->collection_name, 's3-public');
                });

            // Move notification setting logos from private s3 to public s3-public disk
            NotificationSetting::query()
                ->whereHas('media', fn ($query) => $query->where('collection_name', 'logo')->where('disk', 's3'))
                ->each(function (NotificationSetting $setting) {
                    $setting->getMedia('logo')
                        ->where('disk', 's3')
                        ->each(fn (Media $media) => $media->move($setting, 'logo', 's3-public'));
                });

            MediaToPublicDiskFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            MediaToPublicDiskFeature::deactivate();

            $themeModel = ThemeSettings::getSettingsPropertyModel('theme.is_logo_active');

            Media::query()
                ->where('model_type', 'theme_settings_property')
                ->where('model_id', $themeModel->getKey())
                ->whereIn('collection_name', ['logo', 'dark_logo'])
                ->where('disk', 's3-public')
                ->each(function (Media $media) use ($themeModel) {
                    $media->move($themeModel, $media->collection_name, 's3');
                });

            NotificationSetting::query()
                ->whereHas('media', fn ($query) => $query->where('collection_name', 'logo')->where('disk', 's3-public'))
                ->each(function (NotificationSetting $setting) {
                    $setting->getMedia('logo')
                        ->where('disk', 's3-public')
                        ->each(fn (Media $media) => $media->move($setting, 'logo', 's3'));
                });
        });
    }
};
