<?php

use App\Features\KnowledgeBaseCategorySlug;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        KnowledgeBaseCategorySlug::activate();
    }

    public function down(): void
    {
        KnowledgeBaseCategorySlug::deactivate();
    }
};
