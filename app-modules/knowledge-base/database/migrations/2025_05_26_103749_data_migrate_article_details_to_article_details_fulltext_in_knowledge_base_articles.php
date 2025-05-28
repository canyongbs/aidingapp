<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('knowledge_base_articles')->chunkById(100, function ($articles) {
            foreach ($articles as $article) {

                if(!blank($article->article_details)){
                    $articleDetails = strip_tags(tiptap_converter()->asHTML($article->article_details));

                    DB::table('knowledge_base_articles')
                        ->where('id', $article->id)
                        ->update(['article_details_fulltext' => $articleDetails]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            DB::table('knowledge_base_articles')->update([
                'article_details_fulltext' => null,
            ]);
        });
    }
};
