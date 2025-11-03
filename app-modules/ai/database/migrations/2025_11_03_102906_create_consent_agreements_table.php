<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('type');
            $table->string('title');
            $table->longText('description');
            $table->longText('body');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_agreements');
    }
};
