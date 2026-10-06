<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction
    public function up(): void
    {
        try {
            $this->migrator->add('contact_presence.active_threshold', 5);
        } catch (SettingAlreadyExists $exception) {
        }

        try {
            $this->migrator->add('contact_presence.idle_threshold', 30);
        } catch (SettingAlreadyExists $exception) {
        }

        try {
            $this->migrator->add('contact_presence.inactive_threshold', 480);
        } catch (SettingAlreadyExists $exception) {
        }
    }

    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction
    public function down(): void
    {
        $this->migrator->deleteIfExists('contact_presence.active_threshold');
        $this->migrator->deleteIfExists('contact_presence.idle_threshold');
        $this->migrator->deleteIfExists('contact_presence.inactive_threshold');
    }
};
