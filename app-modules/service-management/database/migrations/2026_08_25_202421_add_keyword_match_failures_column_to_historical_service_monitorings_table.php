<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('historical_service_monitorings', function (Blueprint $table) {
            $table->jsonb('keyword_match_failures')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('historical_service_monitorings', function (Blueprint $table) {
            $table->dropColumn('keyword_match_failures');
        });
    }
};
