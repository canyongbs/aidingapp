<?php

use AidingApp\Project\Models\PipelineEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            PipelineEntry::table('pipeline_entries')
                ->with('milestones')
                ->chunkById(100, function ($pipelineEntries) {
                    foreach ($pipelineEntries as $entry) {
                        if ($entry->milestones->isNotEmpty()) {
                            $entry->project_milestone_id = $entry->milestones->first()->id;
                            $entry->save();
                        }
                    }
                });
        });
    }

    public function down(): void {}
};
