<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('portal_assistant_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('thread_id')->constrained('portal_assistant_threads');
            $table->nullableUuidMorphs('author');
            $table->string('message_id')->nullable();
            $table->text('content');
            $table->text('context')->nullable();
            $table->text('request')->nullable();
            $table->jsonb('next_request_options')->nullable();
            $table->boolean('is_advisor')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_assistant_messages');
    }
};
