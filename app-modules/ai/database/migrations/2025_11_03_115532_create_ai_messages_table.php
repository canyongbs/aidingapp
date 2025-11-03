<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('message_id')->nullable();
            $table->text('content');
            $table->text('context')->nullable();
            $table->text('request')->nullable();
            $table->foreignUuid('thread_id')->constrained('ai_threads')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('prompt_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
