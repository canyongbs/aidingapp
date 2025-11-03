<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('ai.llamaparse_model_name');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.llamaparse_api_key', encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('ai.llamaparse_model_name');
            $this->migrator->deleteIfExists('ai.llamaparse_api_key');
        });
    }
};
