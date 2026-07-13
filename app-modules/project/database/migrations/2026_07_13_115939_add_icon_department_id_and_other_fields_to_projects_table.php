<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('icon')->nullable()->default('heroicon-o-clipboard-document-list');
            $table->string('color')->nullable()->default('gray');
            $table->foreignUuid('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('target_completion_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['icon', 'color', 'department_id', 'start_date', 'target_completion_date']);
        });
    }
};
