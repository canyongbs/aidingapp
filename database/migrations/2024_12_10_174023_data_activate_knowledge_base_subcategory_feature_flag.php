<?php

use App\Features\KnowledgeBaseSubcategory;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        KnowledgeBaseSubcategory::activate();
    }

    public function down(): void
    {
        KnowledgeBaseSubcategory::deactivate();
    }
};
