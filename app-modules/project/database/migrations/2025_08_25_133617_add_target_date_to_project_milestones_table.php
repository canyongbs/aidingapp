<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use App\Features\ProjectMilestoneTargetDateFeature;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->date('target_date')->nullable();
        });

        ProjectMilestoneTargetDateFeature::activate();
    }

    public function down(): void
    {
        ProjectMilestoneTargetDateFeature::purge();

        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn('target_date');
        });
    }
};
