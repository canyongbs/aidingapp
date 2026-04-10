<?php

use App\Features\KnowledgeBaseItemConcernFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        KnowledgeBaseItemConcernFeature::activate();
    }

    public function down(): void
    {
        KnowledgeBaseItemConcernFeature::deactivate();
    }
};
