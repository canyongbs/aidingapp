<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Observers;

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\Project\Models\Scopes\ActivePipelineFirst;
use App\Features\AssociateMilestoneWithActivePipelineFeature;
use InvalidArgumentException;

class ProjectMilestoneObserver
{
    public function creating(ProjectMilestone $projectMilestone): void
    {
        if (blank($projectMilestone->created_by_id)) {
            $projectMilestone->created_by_id = auth()->id();
        }

        $this->associateWithActivePipeline($projectMilestone);
    }

    /**
     * Ensure every new milestone relates to exactly one pipeline that belongs to its own project.
     * An explicitly supplied pipeline must belong to the milestone's project; otherwise fall back
     * to the project's active (oldest, non-archived) pipeline, matching how the application
     * resolves the default active pipeline elsewhere.
     */
    protected function associateWithActivePipeline(ProjectMilestone $projectMilestone): void
    {
        // TODO: Cleanup Task (associate-milestone-with-active-pipeline): when the flag is
        // removed, delete this guard (keep the active path below) and the feature import.
        if (! AssociateMilestoneWithActivePipelineFeature::active()) {
            return;
        }

        if (blank($projectMilestone->project_id)) {
            return;
        }

        if (filled($projectMilestone->pipeline_id)) {
            $belongsToProject = Pipeline::query()
                ->whereKey($projectMilestone->pipeline_id)
                ->where('project_id', $projectMilestone->project_id)
                ->exists();

            if (! $belongsToProject) {
                throw new InvalidArgumentException(
                    "Pipeline [{$projectMilestone->pipeline_id}] does not belong to project [{$projectMilestone->project_id}].",
                );
            }

            return;
        }

        $projectMilestone->pipeline_id = Pipeline::query()
            ->where('project_id', $projectMilestone->project_id)
            ->withoutArchived()
            ->tap(new ActivePipelineFirst())
            ->value('id');
    }
}
