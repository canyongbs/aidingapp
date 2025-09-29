<?php

use App\Features\DisplaySettingsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DisplaySettingsFeature::activate();
    }

    public function down(): void
    {
        DisplaySettingsFeature::deactivate();
    }
};
