<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('portal.logo');
        $this->migrator->delete('portal.primary_color');
        $this->migrator->delete('portal.secondary_color');

        $this->migrator->delete('portal.has_user_chat');
        $this->migrator->delete('portal.has_performance_alerts');
        $this->migrator->delete('portal.has_emergency_alerts');
        $this->migrator->delete('portal.has_service_management');
        $this->migrator->delete('portal.has_notifications');
        $this->migrator->delete('portal.has_knowledge_base');
        $this->migrator->delete('portal.has_tasks');
        $this->migrator->delete('portal.has_files_and_documents');
        $this->migrator->delete('portal.has_forms');
    }
};
