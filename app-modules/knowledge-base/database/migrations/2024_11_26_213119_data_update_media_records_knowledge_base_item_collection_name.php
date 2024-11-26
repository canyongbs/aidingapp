<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('media')
            ->where('model_type', 'knowledge_base_item')
            ->where('collection_name', 'solution')
            ->update(['collection_name' => 'article_details']);
    }

    public function down(): void
    {
        DB::table('media')
            ->where('model_type', 'knowledge_base_item')
            ->where('collection_name', 'article_details')
            ->update(['collection_name' => 'solution']);
    }
};
