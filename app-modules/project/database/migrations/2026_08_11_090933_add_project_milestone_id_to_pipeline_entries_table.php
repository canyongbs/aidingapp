<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pipeline_entries', function (Blueprint $table) {
            $table->foreignUuid('project_milestone_id')->nullable()->references('id')->on('project_milestones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pipeline_entries', function (Blueprint $table) {
            $table->dropForeign(['project_milestone_id']);
            $table->dropColumn('project_milestone_id');
        });
    }
};
