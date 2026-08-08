<?php

use App\Features\PipelineArchivingFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            Schema::table('pipelines', function (Blueprint $table): void {
                $table->timestamp('archived_at')->nullable();
            });

            Schema::table('pipeline_entries', function (Blueprint $table): void {
                $table->timestamp('archived_at')->nullable();
            });

            Schema::table('project_milestones', function (Blueprint $table): void {
                $table->timestamp('archived_at')->nullable();
            });

            PipelineArchivingFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            Schema::table('project_milestones', function (Blueprint $table): void {
                $table->dropColumn('archived_at');
            });

            Schema::table('pipeline_entries', function (Blueprint $table): void {
                $table->dropColumn('archived_at');
            });

            Schema::table('pipelines', function (Blueprint $table): void {
                $table->dropColumn('archived_at');
            });

            PipelineArchivingFeature::deactivate();
        });
    }
};
