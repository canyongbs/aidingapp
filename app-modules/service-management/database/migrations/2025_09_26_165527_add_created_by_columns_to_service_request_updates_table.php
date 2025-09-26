<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_updates', function (Blueprint $table) {
            $table->nullableUuidMorphs('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_updates', function (Blueprint $table) {
            $table->dropMorphs('created_by');
        });
    }
};
