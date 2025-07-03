<?php

use App\Features\ConfidentialTaskFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ConfidentialTaskFeature::activate();
    }

    public function down(): void
    {
        ConfidentialTaskFeature::deactivate();
    }
};
