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

namespace AidingApp\Project\Filament\Resources\Pipelines\Pages;

use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Filament\Resources\Pipelines\Resources\PipelineEntries\PipelineEntryResource;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @property Schema $form
 */
class EditPipelineEntry extends Page
{
    use InteractsWithFormActions;
    use InteractsWithRecord;

    protected static string $resource = PipelineEntryResource::class;

    protected static ?string $title = 'Edit Pipeline Task';

    protected string $view = 'project::filament.pages.edit-pipeline-entry';

    /** @var array<string, mixed> $data */
    public ?array $data = [];

    public function mount(int | string $record): void
    {
        try {
            $this->record = $this->resolveRecord($record);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $pipeline = $this->getParentRecord();

        if (! $pipeline instanceof Pipeline) {
            abort(404);
        }

        if ($this->getPipelineEntry()->pipelineStage->pipeline_id !== $pipeline->id) {
            abort(404);
        }

        abort_unless((bool) Filament::auth()->user()?->can('update', $pipeline), 403);

        $this->fillForm();
    }

    public function getPipelineEntry(): PipelineEntry
    {
        $pipelineEntry = $this->getRecord();

        assert($pipelineEntry instanceof PipelineEntry);

        return $pipelineEntry;
    }

    public function getPipeline(): Pipeline
    {
        $pipeline = $this->getParentRecord();

        assert($pipeline instanceof Pipeline);

        return $pipeline;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Edit Pipeline Task';
    }

    public function getBackUrl(): string
    {
        return PipelineEntryResource::getUrl('view', [
            'record' => $this->getPipelineEntry(),
            'pipeline' => $this->getPipeline(),
            'project' => $this->getPipeline()->project,
        ]);
    }

    /**
     * @return array<string>
     */
    public function getResourceBreadcrumbs(): array
    {
        $pipeline = $this->getPipeline();
        $project = $pipeline->project;

        return [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ProjectResource::getUrl('view', ['record' => $project]) => ProjectResource::getRecordTitle($project),
            '' => PipelineResource::getBreadcrumb(),
            PipelineResource::getUrl('view', ['record' => $pipeline, 'project' => $project]) => $pipeline->name ?? '',
            PipelineResource::getUrl('entries', ['record' => $pipeline, 'project' => $project]) => PipelineEntryResource::getBreadcrumb(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $pipeline = $this->getPipeline();

        return $schema
            ->components(PipelineEntryForm::components($pipeline))
            ->statePath('data')
            ->model($this->getPipelineEntry());
    }

    public function save(): void
    {
        $pipelineEntry = $this->getPipelineEntry();
        $pipeline = $this->getPipeline();

        abort_unless((bool) Filament::auth()->user()?->can('update', $pipeline), 403);

        $data = $this->form->getState();

        $milestones = $data['milestones'] ?? [];
        $assets = $data['assets'] ?? [];
        $serviceRequests = $data['serviceRequests'] ?? [];
        unset($data['milestones'], $data['assets'], $data['serviceRequests']);

        $pipelineEntry->update($data);

        $pipelineEntry->milestones()->sync($milestones);
        $pipelineEntry->assets()->sync($assets);
        $pipelineEntry->serviceRequests()->sync($serviceRequests);

        Notification::make()
            ->success()
            ->title('Pipeline task updated successfully')
            ->send();

        $this->redirect($this->getBackUrl());
    }

    public function fillForm(): void
    {
        $pipelineEntry = $this->getPipelineEntry();

        $data = $pipelineEntry->attributesToArray();

        $data['milestones'] = $pipelineEntry->milestones->pluck('id')->toArray();
        $data['assets'] = $pipelineEntry->assets->pluck('id')->toArray();
        $data['serviceRequests'] = $pipelineEntry->serviceRequests->pluck('id')->toArray();

        $this->form->fill($data);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save')
                ->keyBindings(['mod+s']),
            Action::make('cancel')
                ->label('Cancel')
                ->url($this->getBackUrl())
                ->color('gray'),
        ];
    }
}
