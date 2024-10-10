<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('knowledge_base_categories', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
