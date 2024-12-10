<?php

use App\Features\AiSettingsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AiSettingsFeature::activate();
    }

    public function down(): void
    {
        AiSettingsFeature::deactivate();
    }
};
