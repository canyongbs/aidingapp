<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_check_outs', function (Blueprint $table) {
            $table->dropColumn('checked_out_to_type');
        });
    }

    public function down(): void
    {
        Schema::table('asset_check_outs', function (Blueprint $table) {
            $table->string('checked_out_to_type');
        });
    }
};
