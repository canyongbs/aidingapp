<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_providers', function (Blueprint $table) {
            $table->integer('asset_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_providers', function (Blueprint $table) {
            $table->dropColumn('asset_count');
        });
    }
};
