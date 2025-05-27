<?php

use App\Features\ArticleFullTextSearch;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ArticleFullTextSearch::activate();
    }

    public function down(): void
    {
        ArticleFullTextSearch::deactivate();
    }
};
