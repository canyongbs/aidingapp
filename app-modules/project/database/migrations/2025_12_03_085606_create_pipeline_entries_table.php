<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('pipeline_stage_id')->references('id')->on('pipeline_stages');
            // $table->foreignUuid('pipeline_entry_type_id')->references('id')->on('pipeline_entry_types');
            $table->uuidMorphs('organizable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_entries');
    }
};
