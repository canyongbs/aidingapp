<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('portal.knowledge_management_portal_authorized_domain');
    }

    public function down(): void
    {
        $this->migrator->add('portal.knowledge_management_portal_authorized_domain');
    }
};
