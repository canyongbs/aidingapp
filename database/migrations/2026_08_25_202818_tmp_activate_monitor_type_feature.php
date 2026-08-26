<?php

use App\Features\MonitorTypeFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        MonitorTypeFeature::activate();
    }

    public function down(): void
    {
        MonitorTypeFeature::deactivate();
    }
};
