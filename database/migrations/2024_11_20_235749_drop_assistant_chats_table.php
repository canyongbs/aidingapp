<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('assistant_chat_folders');
    }

    public function down(): void
    {
        Schema::create('assistant_chats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assistant_chat_folder_id')->nullable()->constrained('assistant_chat_folders')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
