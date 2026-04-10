<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('manager_knowledge_base_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('manager_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('knowledge_base_item_id')->constrained('knowledge_base_articles')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_knowledge_base_items');
    }
};
