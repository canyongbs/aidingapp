<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('assistant_chat_message_logs');
    }

    public function down(): void
    {
        Schema::create('assistant_chat_message_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->longText('message');
            $table->longText('metadata');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->longText('request');
            $table->timestamp('sent_at');

            $table->timestamps();
        });
    }
};
