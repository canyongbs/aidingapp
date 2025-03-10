<?php

use App\Features\UserWorkFieldsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        UserWorkFieldsFeature::activate();
    }

    public function down(): void
    {
        UserWorkFieldsFeature::deactivate();
    }
};
