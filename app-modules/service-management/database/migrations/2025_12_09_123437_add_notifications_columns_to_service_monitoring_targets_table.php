<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_monitoring_targets', function (Blueprint $table) {
            $table->boolean('is_notified_via_database')->default(false);
            $table->boolean('is_notified_via_email')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_monitoring_targets', function (Blueprint $table) {
            $table->dropColumn('is_notified_via_database');
            $table->dropColumn('is_notified_via_email');
        });
    }
};
