<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('knowledge_base_article_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_helpful');
            $table->uuid('user_id');
            $table->string('user_type');
            $table->foreignUuid('article_id')->constrained('knowledge_base_articles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_article_votes');
    }
};
