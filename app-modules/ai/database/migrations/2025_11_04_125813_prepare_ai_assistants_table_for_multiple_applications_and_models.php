<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->string('application')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('model')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->dropColumn('application');
            $table->dropColumn('model');
            $table->dropColumn('is_default');
        });
    }
};
