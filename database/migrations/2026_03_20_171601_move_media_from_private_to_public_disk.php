<?php

use AidingApp\Theme\Settings\ThemeSettings;
use App\Features\MediaToPublicDiskFeature;
use App\Models\NotificationSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration
{
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
