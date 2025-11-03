<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('prompt');

            $table->foreignUuid('type_id')->constrained('prompt_types')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_smart')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex(['title'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
