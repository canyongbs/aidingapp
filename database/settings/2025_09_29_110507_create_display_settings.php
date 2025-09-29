<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('display.timezone');
    }

    public function down(): void
    {
        $this->migrator->delete('display.timezone');
    }
};
