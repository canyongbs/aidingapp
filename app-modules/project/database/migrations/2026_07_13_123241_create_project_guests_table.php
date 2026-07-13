<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('project_guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->nullableUuidMorphs('guest');
            $table->timestamps();

            $table->unique(['project_id', 'guest_id', 'guest_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_guests');
    }
};
