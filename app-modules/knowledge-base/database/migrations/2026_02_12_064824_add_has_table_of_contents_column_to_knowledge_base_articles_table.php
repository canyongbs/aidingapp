<?php

use App\Features\KnowledgeBaseArticleTableOfContents;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->boolean('has_table_of_contents')->default(false);
            });

            KnowledgeBaseArticleTableOfContents::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            KnowledgeBaseArticleTableOfContents::deactivate();

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->dropColumn('has_table_of_contents');
            });
        });
    }
};
