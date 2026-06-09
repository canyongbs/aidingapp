<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropUnique('tenants_domain_unique');

                $table->uniqueIndex('domain', 'tenants_domain_unique')->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropIndex('tenants_domain_unique');
                $table->unique('domain');
            });
        });
    }
};
