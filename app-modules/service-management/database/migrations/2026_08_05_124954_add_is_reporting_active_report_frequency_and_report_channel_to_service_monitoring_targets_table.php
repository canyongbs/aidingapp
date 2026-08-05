<?php

use App\Features\ServiceMonitoringReportFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_monitoring_targets', function (Blueprint $table) {
                $table->boolean('is_reporting_active')->default(false);
                $table->string('report_frequency')->nullable();
                $table->boolean('is_reported_via_database')->default(false);
                $table->boolean('is_reported_via_email')->default(false);
            });

            ServiceMonitoringReportFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ServiceMonitoringReportFeature::deactivate();

            Schema::table('service_monitoring_targets', function (Blueprint $table) {
                $table->dropColumn(['is_reporting_active', 'report_frequency', 'is_reported_via_database', 'is_reported_via_email']);
            });
        });
    }
};
