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

use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Filament\Actions\ProjectManageAccessAction;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class ProjectDashboardHeaderWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Locked]
    public Project $record;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-dashboard-header-widget';

    public function editProjectAction(): Action
    {
        return Action::make('editProject')
            ->label('Edit Project')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->extraAttributes(['class' => 'grow'])
            ->authorize(fn (): bool => auth()->user()->can('update', $this->record))
            ->url(fn (): string => ProjectResource::getUrl('edit', ['record' => $this->record]));
    }

    public function manageAccessAction(): Action
    {
        return ProjectManageAccessAction::make('manageAccess')
            ->extraAttributes(['class' => 'grow'])
            ->record($this->record);
    }

    public function getProgress(): int
    {
        $entries = PipelineEntry::query()
            ->whereHas(
                'pipelineStage.pipeline',
                fn (Builder $query) => $query->where('project_id', $this->record->getKey()),
            );

        $totalEntriesCount = $entries->clone()->count();

        if ($totalEntriesCount === 0) {
            return 0;
        }

        $completeEntriesCount = $entries->clone()
            ->whereHas(
                'pipelineStage',
                fn (Builder $query) => $query->where('classification', PipelineStageClassification::Complete->value),
            )
            ->count();

        return (int) round(($completeEntriesCount / $totalEntriesCount) * 100);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'project' => $this->record,
            'progress' => $this->getProgress(),
        ];
    }
}
