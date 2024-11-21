<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('prompts');
    }

    public function down(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('prompt');

            $table->foreignUuid('type_id')->constrained('prompt_types')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex(['title'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
        });
    }
};
