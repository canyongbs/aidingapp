<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use Filament\Notifications\Notification;
use Livewire\Component;

class PipelineEntriesKanban extends Component
{
    public Pipeline $pipeline;

    public function mount(Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function movedTask(string $entryId, string $fromStageId, string $toStageId): array
    {
        try {
            $entry = PipelineEntry::findOrFail($entryId);
            
            // Check authorization if needed
            // $this->authorize('update', $entry);
            
            $entry->update([
                'stage_id' => $toStageId,
            ]);

            return [
                'success' => true,
                'message' => 'Entry moved successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to move entry: ' . $e->getMessage(),
            ];
        }
    }

    public function render()
    {
        $stages = $this->pipeline->stages()->with(['entries.entryable'])->get();

        return view('project::livewire.pipeline-entries-kanban', [
            'stages' => $stages,
        ]);
    }
}
