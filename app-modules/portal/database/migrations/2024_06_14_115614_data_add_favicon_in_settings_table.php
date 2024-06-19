<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('portal.favicon');
    }

    public function down(): void
    {
        $this->migrator->delete('portal_favicon');
    }
};
