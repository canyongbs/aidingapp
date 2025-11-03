<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('ai.url');
        $this->migrator->delete('ai.key');
        $this->migrator->delete('ai.api_version');
        $this->migrator->delete('ai.model');
    }
};
