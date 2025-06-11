<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('portal.page_title', 'Help Center');
    }

    public function down(): void
    {
        $this->migrator->delete('portal.page_title');
    }
};
