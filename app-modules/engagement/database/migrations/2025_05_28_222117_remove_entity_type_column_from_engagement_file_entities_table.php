<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagement_file_entities', function (Blueprint $table) {
            $table->dropColumn('entity_type');
        });
    }

    public function down(): void
    {
        Schema::table('engagement_file_entities', function (Blueprint $table) {
            $table->string('entity_type');
        });
    }
};
