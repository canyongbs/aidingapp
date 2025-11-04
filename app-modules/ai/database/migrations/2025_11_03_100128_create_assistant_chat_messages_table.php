<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
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

    public function down(): void
    {
        Schema::dropIfExists('assistant_chat_messages');
    }
};
