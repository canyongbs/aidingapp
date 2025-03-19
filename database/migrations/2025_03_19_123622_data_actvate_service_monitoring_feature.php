<?php

use App\Features\ServiceMonitoring;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ServiceMonitoring::activate();
    }

    public function down(): void
    {
        ServiceMonitoring::deactivate();
    }
};
