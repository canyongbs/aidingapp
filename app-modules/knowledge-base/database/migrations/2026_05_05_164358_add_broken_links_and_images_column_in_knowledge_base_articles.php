<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->boolean('are_broken_links_detected')->default(false);
                $table->jsonb('broken_links')->nullable();
                $table->boolean('are_broken_images_detected')->default(false);
                $table->jsonb('broken_images')->nullable();
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->dropColumn('are_broken_links_detected');
                $table->dropColumn('broken_links');
                $table->dropColumn('are_broken_images_detected');
                $table->dropColumn('broken_images');
            });
        });
    }
};
