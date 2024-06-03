<?php

use Laravel\Pennant\Feature;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('portal.knowledge_management_portal_layout', null);

        Feature::activate('portal-configuration-options');
    }

    public function down(): void
    {
        $this->migrator->delete('portal.knowledge_management_portal_layout');

        Feature::deactivate('portal-configuration-options');
    }
};
