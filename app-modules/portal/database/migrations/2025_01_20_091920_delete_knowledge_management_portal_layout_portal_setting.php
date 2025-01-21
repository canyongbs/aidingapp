<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->deleteIfExists('portal.knowledge_management_portal_layout');
    }

    public function down(): void
    {
        $this->migrator->add('portal.knowledge_management_portal_layout', null);
    }
};
