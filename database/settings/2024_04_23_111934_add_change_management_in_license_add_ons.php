<?php

use App\Settings\LicenseSettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $data = app(LicenseSettings::class)->data;
        $data = @json_decode(json_encode($data), true);

        /** Add new settings property */

        $data['addons']['change_management'] = true;

        /** Deleting existing data group */
        
        $this->migrator->delete('license.data');

        /** Creating License data group again with new propery with existing data */

        $this->migrator->addEncrypted(
            'license.data',
            $data
        );
    }
};
