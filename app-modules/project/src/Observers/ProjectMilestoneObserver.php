<?php

namespace AidingApp\Project\Observers;

use AidingApp\Project\Models\ProjectMilestone;

class ProjectMilestoneObserver
{
    public function creating(ProjectMilestone $projectMilestone): void
    {
        if (blank($projectMilestone->created_by_id)) {
            $projectMilestone->created_by_id = auth()->id();
        }
    }
}
