<?php

use App\Features\ArticleFullTextSearch;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ArticleFullTextSearch::activate();
    }

    public function down(): void
    {
        ArticleFullTextSearch::deactivate();
    }
};
