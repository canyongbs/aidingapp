<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

/**
 * @property Form $form
 */
class EditPipelineEntry extends Page
{
    use InteractsWithFormActions;

    protected static string $resource = PipelineResource::class;

    protected static ?string $title = 'Edit Pipeline Entry';

    protected static string $view = 'project::filament.pages.edit-pipeline-entry';

    public Pipeline $record;

    public PipelineEntry $pipelineEntry;

    /** @var array<string, mixed> $data */
    public ?array $data = [];

    #[Locked, Url]
    public ?string $project = null;

    public function mount(): void
    {
        if ($this->pipelineEntry->pipelineStage->pipeline_id !== $this->record->id) {
            abort(404);
        }

        $this->fillForm();
    }

    public function getTitle(): string | Htmlable
    {
        return 'Edit Pipeline Entry';
    }

    public function getBackUrl(): string
    {
        $source = session('pipeline_entry_source', 'list');

        $params = [
            'record' => $this->record,
            'pipelineEntry' => $this->pipelineEntry,
            'from' => $source,
        ];

        if ($this->project) {
            $params['project'] = $this->project;
        }

        return PipelineResource::getUrl('view-pipeline-entry', $params);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $pipeline = $this->record;
        $project = $pipeline->project;

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            PipelineResource::getUrl('view', ['record' => $this->record]) => Str::limit('Pipelines', 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    Select::make('pipeline_stage_id')
                        ->label('Stage')
                        ->relationship('pipelineStage', 'name')
                        ->required()
                        ->options(fn () => $this->record->stages->pluck('name', 'id')),
                    Textarea::make('name')
                        ->maxLength(255)
                        ->label('Description')
                        ->string(),
                ]),
                MorphToSelect::make('organizable')
                    ->types([
                        Type::make(Contact::class)
                            ->label('Contact')
                            ->titleAttribute('full_name')
                            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->limit(50)),
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->statePath('data')
            ->model($this->pipelineEntry);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->pipelineEntry->update($data);

        Notification::make()
            ->success()
            ->title('Pipeline entry updated successfully')
            ->send();

        $this->redirect($this->getBackUrl());
    }

    public function fillForm(): void
    {
        $data = $this->pipelineEntry->attributesToArray();

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
