<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->text('article_details_fulltext')->nullable();

            // Add generated search_vector column
            $table->tsVector('search_vector')->storedAs("
                setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('english', coalesce(article_details_fulltext, '')), 'B')
            ");
        });

        DB::statement('CREATE INDEX knowledge_base_articles_search_vector_idx ON knowledge_base_articles USING GIN(search_vector)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS knowledge_base_articles_search_vector_idx');

        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->dropColumn('search_vector');
            $table->dropColumn('article_details_fulltext');
        });
    }
};
