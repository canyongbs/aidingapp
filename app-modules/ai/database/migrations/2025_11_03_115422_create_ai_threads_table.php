<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('thread_id')->nullable();
            $table->string('name')->nullable();
            $table->foreignUuid('assistant_id')->constrained('ai_assistants');
            $table->foreignUuid('folder_id')->nullable()->constrained('ai_thread_folders')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained();
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('saved_at')->nullable();
            $table->integer('cloned_count')->default(0);
            $table->integer('emailed_count')->default(0);
            $table->string('locked_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_threads');
    }
};
