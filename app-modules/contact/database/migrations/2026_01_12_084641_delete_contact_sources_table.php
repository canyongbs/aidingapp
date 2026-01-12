<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('source_id');
            });

            Schema::dropIfExists('contact_sources');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::create('contact_sources', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table('contacts', function (Blueprint $table) {
                $table->uuid('source_id')->nullable();
            });
        });
    }
};
