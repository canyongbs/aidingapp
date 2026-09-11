<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('notifications.from_name');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('notifications.logo');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('notifications.primary_color');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('notifications.from_name');
            $this->migrator->deleteIfExists('notifications.logo');
            $this->migrator->deleteIfExists('notifications.primary_color');
        });
    }
};
