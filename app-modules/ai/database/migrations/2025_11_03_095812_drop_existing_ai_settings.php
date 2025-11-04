<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('ai.url');
        $this->migrator->delete('ai.key');
        $this->migrator->delete('ai.api_version');
        $this->migrator->delete('ai.model');
    }
};
