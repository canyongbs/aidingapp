<?php

use App\Features\KnowledgeBaseItemBrokenLinksFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('knowledge_base_items', function (Blueprint $table) {
            $table->dropForeign(['quality_id']);
            $table->dropColumn('quality_id');
        });

    }

    public function down(): void
    {
        Schema::table('knowledge_base_items', function (Blueprint $table) {
            $table->foreign('quality_id')->references('id')->on('knowledge_base_qualities');
        });
    }
};
