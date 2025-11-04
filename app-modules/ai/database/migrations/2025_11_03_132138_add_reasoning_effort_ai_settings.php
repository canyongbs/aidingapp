<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('ai.reasoning_effort', 'medium');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('ai.reasoning_effort');
        });
    }
};
