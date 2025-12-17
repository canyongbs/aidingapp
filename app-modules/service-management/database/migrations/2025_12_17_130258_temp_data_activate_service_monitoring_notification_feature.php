<?php

use App\Features\ServiceMonitoringNotificationFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ServiceMonitoringNotificationFeature::activate();
    }

    public function down(): void
    {
        ServiceMonitoringNotificationFeature::deactivate();
    }
};
