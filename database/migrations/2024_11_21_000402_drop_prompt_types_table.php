<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('prompt_types');
    }

    public function down(): void
    {
        Schema::create('prompt_types', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->longText('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex(['title'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
        });
    }
};
