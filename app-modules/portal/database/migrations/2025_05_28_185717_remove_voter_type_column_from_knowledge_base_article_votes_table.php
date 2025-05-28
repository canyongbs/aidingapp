<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base_article_votes', function (Blueprint $table) {
            $table->dropColumn('voter_type');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_article_votes', function (Blueprint $table) {
            $table->string('voter_type');
        });
    }
};
