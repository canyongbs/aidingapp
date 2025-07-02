<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::table('knowledge_base_categories', function (Blueprint $table) {
            DB::transaction(function () use ($table) {
                $table->dropUnique('knowledge_base_categories_slug_unique');

                 $table->uniqueIndex('slug')
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_categories', function (Blueprint $table) {
            DB::transaction(function () use ($table) {
                $table->dropUnique('knowledge_base_categories_slug_unique');

                $table->string('slug')->unique()->change();
            });
        });
    }
};
