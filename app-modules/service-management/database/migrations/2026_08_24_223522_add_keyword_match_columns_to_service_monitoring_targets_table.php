<?php

use AidingApp\ServiceManagement\Enums\MonitorType;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_monitoring_targets', function (Blueprint $table) {
            $table->string('monitor_type')->initial(MonitorType::Availability);
            $table->jsonb('should_contain')->nullable();
            $table->jsonb('should_not_contain')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('service_monitoring_targets', function (Blueprint $table) {
            $table->dropColumn('monitor_type');
            $table->dropColumn('should_contain');
            $table->dropColumn('should_not_contain');
        });
    }
};
