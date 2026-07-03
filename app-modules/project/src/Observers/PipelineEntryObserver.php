<?php

namespace AidingApp\Project\Observers;

use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Notifications\PipelineEntryAssignedToUserNotification;

class PipelineEntryObserver
{
    public function saving(PipelineEntry $pipelineEntry): void
    {
        if (is_null($pipelineEntry->created_by)) {
            $pipelineEntry->created_by = auth()->id();
        }
    }

    public function saved(PipelineEntry $pipelineEntry): void
    {
        if ($pipelineEntry->wasChanged('assigned_to') && ! is_null($pipelineEntry->assigned_to)) {
            $pipelineEntry->assignedTo->notify(new PipelineEntryAssignedToUserNotification($pipelineEntry));
        }
    }
}
