<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->string('assistant_id')->nullable();
            $table->string('thread_id')->nullable();
        });

        Schema::table('assistant_chat_messages', function (Blueprint $table) {
            $table->string('message_id')->nullable();
            $table->string('run_id')->nullable();
            $table->json('file_ids')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->dropColumn('assistant_id');
            $table->dropColumn('thread_id');
        });

        Schema::table('assistant_chat_messages', function (Blueprint $table) {
            $table->dropColumn('message_id');
            $table->dropColumn('run_id');
            $table->dropColumn('file_ids');
        });
    }
};
