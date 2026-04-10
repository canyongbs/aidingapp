<?php

use App\Features\KnowledgeBaseItemConcernFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function() {
            Schema::create('knowledge_base_item_concerns', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('description');
                $table->string('status');
                $table->foreignUuid('created_by_id')->constrained('users');
                $table->foreignUuid('last_updated_by_id')->constrained('users');
                $table->foreignUuid('knowledge_base_item_id')->constrained('knowledge_base_items');
                
                $table->timestamps();
                $table->softDeletes();
            });

            KnowledgeBaseItemConcernFeature::activate();
        });
        
    }

    public function down(): void
    {
        DB::transaction(function () {
            KnowledgeBaseItemConcernFeature::deactivate();

            Schema::dropIfExists('knowledge_base_item_concerns');
        });
    }
};
