<?php

use App\Features\PipelineEntryStartDateFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            Schema::table('pipeline_entries', function (Blueprint $table): void {
                $table->timestamp('start_date')->nullable();
            });

            PipelineEntryStartDateFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            Schema::table('pipeline_entries', function (Blueprint $table): void {
                $table->dropColumn('start_date');
            });

            PipelineEntryStartDateFeature::deactivate();
        });
    }
};
