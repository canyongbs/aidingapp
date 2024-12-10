<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai.url', '', encrypted: true);
        $this->migrator->add('ai.key', '', encrypted: true);
        $this->migrator->add('ai.api_version', '', encrypted: true);
        $this->migrator->add('ai.model', '', encrypted: true);
    }

    public function down(): void
    {
        $this->migrator->delete('ai.url');
        $this->migrator->delete('ai.key');
        $this->migrator->delete('ai.api_version');
        $this->migrator->delete('ai.model');
    }
};
