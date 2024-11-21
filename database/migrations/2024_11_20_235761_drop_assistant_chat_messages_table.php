<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('assistant_chat_messages');
    }

    public function down(): void
    {
        Schema::create('assistant_chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('assistant_chat_id')->constrained('assistant_chats')->onDelete('cascade');
            $table->string('from');
            $table->longText('message')->nullable();
            $table->string('name')->nullable();
            $table->json('function_call')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
