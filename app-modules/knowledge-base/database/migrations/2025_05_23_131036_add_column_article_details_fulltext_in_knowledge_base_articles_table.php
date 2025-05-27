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
            $table->fullText(['title', 'article_details_fulltext'])->language('english');
            $table->tsVector('search_vector')->nullable();
        });

        DB::statement('CREATE INDEX knowledge_base_articles_search_vector_idx ON knowledge_base_articles USING GIN(search_vector)');
        DB::statement("
            CREATE FUNCTION update_knowledge_base_articles_search_vector() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('english', coalesce(NEW.title, '')), 'A') ||
                    setweight(to_tsvector('english', coalesce(NEW.article_details_fulltext, '')), 'B');
                RETURN NEW;
            END
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement('
            CREATE TRIGGER tsvectorupdate BEFORE INSERT OR UPDATE
            ON knowledge_base_articles FOR EACH ROW EXECUTE FUNCTION update_knowledge_base_articles_search_vector();
        ');

        DB::statement("
            UPDATE knowledge_base_articles SET search_vector =
                setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('english', coalesce(article_details_fulltext, '')), 'B');
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS tsvectorupdate ON knowledge_base_articles');
        DB::statement('DROP FUNCTION IF EXISTS update_knowledge_base_articles_search_vector');
        DB::statement('DROP INDEX IF EXISTS knowledge_base_articles_search_vector_idx');

        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->dropFullText(['title', 'article_details_fulltext']);
            $table->dropColumn('search_vector');
            $table->dropColumn('article_details_fulltext');
        });
    }
};
