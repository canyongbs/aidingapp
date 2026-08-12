<?php

use App\Features\PipelineEntryMilestoneFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement(<<<'SQL'
                UPDATE pipeline_entries
                SET project_milestone_id = first_milestone.project_milestone_id
                FROM (
                    SELECT DISTINCT ON (pipeline_entry_id)
                        pipeline_entry_id,
                        project_milestone_id
                    FROM pipeline_entry_milestones
                    ORDER BY pipeline_entry_id, created_at, id
                ) AS first_milestone
                WHERE pipeline_entries.id = first_milestone.pipeline_entry_id
                    AND pipeline_entries.project_milestone_id IS NULL
                SQL);

            PipelineEntryMilestoneFeature::activate();
        });
    }

    public function down(): void
    {
        PipelineEntryMilestoneFeature::deactivate();
    }
};
