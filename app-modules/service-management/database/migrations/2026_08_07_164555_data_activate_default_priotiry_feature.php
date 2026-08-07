<?php

use App\Features\DefaultPriorityFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DefaultPriorityFeature::activate();
    }

    public function down(): void
    {
        DefaultPriorityFeature::deactivate();
    }
};
