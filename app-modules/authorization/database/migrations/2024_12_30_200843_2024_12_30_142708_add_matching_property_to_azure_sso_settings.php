<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('azure_sso', function (SettingsBlueprint $blueprint): void {
            try {
                $blueprint->add('matching_property', 'user_principal_name');
            } catch (SettingAlreadyExists $e) {
                // Ignore
            }
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('azure_sso', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('matching_property');
        });
    }
};
