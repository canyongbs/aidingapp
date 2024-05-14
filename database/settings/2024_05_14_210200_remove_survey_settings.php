<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->deleteIfExists('portal.has_surveys');
    }

    public function down(): void
    {
        $this->migrator->add('portal.has_surveys', false);
    }
};
