<?php

use App\Features\ProjectNewFieldsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectNewFieldsFeature::activate();
    }

    public function down(): void
    {
        ProjectNewFieldsFeature::deactivate();
    }
};
