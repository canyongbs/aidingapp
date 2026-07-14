<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn(['address_2', 'address_3', 'city', 'state', 'postal']);
            });

            DB::table('contacts')->update(['address' => null]);
            
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropColumn(['city', 'state', 'postalcode', 'country']);
            });

            DB::table('organizations')->update(['address' => null]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('address_2')->nullable();
                $table->string('address_3')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal')->nullable();
            });
            
            Schema::table('organizations', function (Blueprint $table) {
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postalcode')->nullable();
                $table->string('country')->nullable();
            });
        });
    }
};
