<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('asset_check_ins', function (Blueprint $table) {
        //     $table->foreignUuid('checked_in_from_id')->constrained('contacts')->change();
        // });
    }

    public function down(): void
    {
        // Schema::table('asset_check_ins', function (Blueprint $table) {
        //     $table->string('checked_in_from_id')->change();
        // });
    }
};
