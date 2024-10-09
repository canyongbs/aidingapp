<?php

use App\Features\ArticleWasHelpful;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ArticleWasHelpful::activate();
    }

    public function down(): void
    {
        ArticleWasHelpful::deactivate();
    }
};
