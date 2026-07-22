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

namespace AidingApp\Project\Filament\Resources\Projects\Widgets;

use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class ProjectStatsWidget extends BaseWidget
{
    #[Locked]
    public Project $record;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', Project::class);
    }

    protected function getStats(): array
    {
        $project = $this->record;

        return [
            Stat::make('files', $project->files()->count())
                ->label('Files'),
            Stat::make('pipeline_tasks', PipelineEntry::query()
                ->whereHas(
                    'pipelineStage.pipeline',
                    fn (Builder $query) => $query->where('project_id', $project->getKey()),
                )
                ->count())
                ->label('Pipeline Tasks'),
            Stat::make('milestones', $project->milestones()->count())
                ->label('Milestones'),
        ];
    }
}
