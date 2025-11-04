<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('ai.url');
            $this->migrator->deleteIfExists('ai.key');
            $this->migrator->deleteIfExists('ai.api_version');
            $this->migrator->deleteIfExists('ai.model');
        });
    }
};
