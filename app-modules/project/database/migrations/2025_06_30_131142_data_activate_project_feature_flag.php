<?php

use App\Features\ProjectFeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectFeatureFlag::activate();
    }

    public function down(): void
    {
        ProjectFeatureFlag::deactivate();
    }
};
