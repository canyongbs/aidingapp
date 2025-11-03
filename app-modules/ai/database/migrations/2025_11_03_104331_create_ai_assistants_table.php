<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('assistant_id')->nullable();

            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->longText('knowledge')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->foreignUuid('created_by_id')
                ->nullable()
                ->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->foreignUuid('ai_assistant_id')->nullable()->constrained('ai_assistants');
        });
    }

    public function down(): void
    {
        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->dropColumn('ai_assistant_id');
        });

        Schema::dropIfExists('ai_assistants');
    }
};
