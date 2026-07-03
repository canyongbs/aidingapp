<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pipeline_entries', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->timestamp('due')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users');
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->string('related_to')->nullable();

            $table->index(['related_to']);
        });
    }

    public function down(): void
    {
        Schema::table('pipeline_entries', function (Blueprint $table) {
            $table->dropColumn(['description', 'due', 'assigned_to', 'created_by', 'related_to']);
            $table->dropIndex(['related_to']);
        });
    }
};
