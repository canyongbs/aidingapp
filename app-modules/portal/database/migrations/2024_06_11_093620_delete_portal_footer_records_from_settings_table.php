<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('portal.footer_color');
        $this->migrator->delete('portal.footer_copyright_statement');
    }

    public function down(): void
    {
        $this->migrator->add('portal.footer_color');
        $this->migrator->add('portal.footer_copyright_statement');
    }
};
