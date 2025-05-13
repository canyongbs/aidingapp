<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('asset_check_outs', function (Blueprint $table) {
        //     $table->foreignUuid('checked_out_to_id')->constrained('contacts')->change();
        // });
    }

    public function down(): void
    {
        // Schema::table('asset_check_outs', function (Blueprint $table) {
        //     $table->string('checked_out_to_id')->change();
        // });
    }
};
