<?php

namespace AidingApp\Project\Observers;

use AidingApp\Project\Models\Project;
use App\Features\ProjectFeatureFlag;

class ProjectObserver
{
    /**
     * Handle the Project "creating" event.
     */
    public function creating(Project $project): void
    {
        if (ProjectFeatureFlag::active() && ! $project->createdBy) {
            $user = auth()->user();
            $project->createdBy()->associate($user);
        }
    }
}
