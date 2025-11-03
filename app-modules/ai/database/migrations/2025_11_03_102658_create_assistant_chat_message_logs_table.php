<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assistant_chat_message_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->longText('message');
            $table->longText('metadata');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->longText('request');
            $table->timestamp('sent_at');
            $table->string('ai_assistant_name')->nullable();
            $table->string('feature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_chat_message_logs');
    }
};
