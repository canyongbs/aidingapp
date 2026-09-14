<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('division_knowledge_base_item');
    }

    public function down(): void
    {
        Schema::create('division_knowledge_base_item', function (Blueprint $table) {
            $table->foreignUuid('knowledge_base_item_id')->references('id')->on('knowledge_base_articles')->onDelete('cascade');
            $table->foreignUuid('division_id')->references('id')->on('divisions');
        });
    }
};
