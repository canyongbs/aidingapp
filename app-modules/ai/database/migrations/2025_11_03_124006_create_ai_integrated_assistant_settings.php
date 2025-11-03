<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai-integrated-assistant.default_model', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai-integrated-assistant.default_model');
    }
};
