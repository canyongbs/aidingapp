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
use AidingApp\Project\Filament\Actions\CreateProjectMilestoneAction;
use AidingApp\Project\Filament\Concerns\HasPipelineSwitcherAction;
use AidingApp\Project\Filament\Resources\Pipelines\Actions\EditPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Actions\ViewPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\Project;
use App\Features\PipelineArchivingFeature;
use AidingApp\Project\Models\ProjectMilestone;
use App\Features\PipelineEntryMilestoneFeature;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class ProjectWorkPipelineWidget extends TableWidget
{
    use HasPipelineSwitcherAction;

    #[Locked]
    public Project $record;

    public ?string $selectedPipelineId = null;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-work-pipeline-widget';

    public function mount(): void
    {
        $this->selectedPipelineId = $this->record
            ->pipelines()
            ->when(
                PipelineArchivingFeature::active(),
                fn (Builder $query): Builder => $query->withoutArchived(),
            )
            ->oldest()
            ->value('id');
    }

    public function getSelectedPipeline(): ?Pipeline
    {
        if (blank($this->selectedPipelineId)) {
            return null;
        }

        return $this->record
            ->pipelines()
            ->when(
                PipelineArchivingFeature::active(),
                fn (Builder $query): Builder => $query->withoutArchived(),
            )
            ->whereKey($this->selectedPipelineId)
            ->first();
    }

    public function table(Table $table): Table
    {
        $pipeline = $this->getSelectedPipeline();

        return $table
            ->query(function () use ($pipeline): Builder {
                if (! $pipeline) {
                    return PipelineEntry::query()->whereRaw('1 = 0');
                }

                return PipelineEntry::query()
                    ->whereHas('pipelineStage', fn (Builder $query) => $query->where('pipeline_id', $pipeline->getKey()))
                    ->when(
                        PipelineArchivingFeature::active(),
                        fn (Builder $query): Builder => $query->withoutArchived(),
                    )
                    ->with([
                        'assets',
                        'serviceRequests',
                        'pipelineStage.pipeline.project',
                        ...(PipelineEntryMilestoneFeature::active() ? ['milestone'] : ['milestones']),
                    ]);
            })
            ->heading(fn (): View => $this->getTableHeadingView($pipeline))
            ->columns([
                TextColumn::make('name')
                    ->label('Task Name')
                    ->searchable(['pipeline_entries.name'])
                    ->sortable()
                    ->extraAttributes(['class' => 'underline'])
                    ->action(function (PipelineEntry $record): void {
                        $this->openPipelineEntry($record);
                    }),
                // TODO:: PipelineEntryMilestoneFeature clean up: Please remove the entire ViewColumn below, along with its corresponding Blade file.
                ViewColumn::make('milestones')
                    ->label('Milestones')
                    ->visible(fn (): bool => ! PipelineEntryMilestoneFeature::active())
                    ->view('project::filament.tables.columns.pipeline-entry.milestones'),
                TextColumn::make('pipelineStage.name')
                    ->label('Stage')
                    ->visible(fn (): bool => PipelineEntryMilestoneFeature::active())
                    ->badge()
                    ->placeholder('N/A'),
                ViewColumn::make('assets')
                    ->label('Assets')
                    ->view('project::filament.tables.columns.pipeline-entry.assets'),
                ViewColumn::make('serviceRequests')
                    ->label('Tickets')
                    ->view('project::filament.tables.columns.pipeline-entry.tickets'),
                IconColumn::make('is_visible_to_guests')
                    ->label('Customer Visible')
                    ->boolean(),
                TextColumn::make('due')
                    ->label('Target Date')
                    ->date()
                    ->placeholder('N/A')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('N/A'),
            ])
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(50)
            ->defaultGroup(
                function () use ($pipeline) {
                    if (! PipelineEntryMilestoneFeature::active()) {
                        return Group::make('pipelineStage.name')
                            ->label('Stage')
                            ->collapsible();
                    }

                    return Group::make('milestone.title')
                        ->label('Milestone')
                        ->getTitleFromRecordUsing(
                            fn (PipelineEntry $record): string => $record->milestone->title ?? 'Unaffiliated'
                        )
                        ->getDescriptionFromRecordUsing(
                            fn (PipelineEntry $record): string => $this->milestoneProgressDescription($record, $pipeline)
                        )
                        ->collapsible();
                }
            )
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->modalHeading('Edit Pipeline Task')
                    ->schema($this->entryFormSchema($pipeline))
                    ->authorize(fn (): bool => auth()->user()->can('update', $this->record))
                    ->after(function (PipelineEntry $record, array $data): void {
                        //TODO: PipelineEntryMilestoneFeature clean up: Please remove the entire if block below.
                        if (! PipelineEntryMilestoneFeature::active()) {
                            $record->milestones()->sync($data['milestones'] ?? []);
                        }
                        $record->assets()->sync($data['assets'] ?? []);
                        $record->serviceRequests()->sync($data['serviceRequests'] ?? []);

                        $this->dispatch('projectPipelineUpdated');
                    }),
                Action::make('editMilestone')
                    ->label('Edit Milestone')
                    ->icon('heroicon-m-pencil-square')
                    ->slideOver()
                    ->fillForm(fn (PipelineEntry $record): array => $record->milestone?->attributesToArray() ?? [])
                    ->schema(CreateProjectMilestoneAction::formSchema())
                    ->visible(fn (PipelineEntry $record): bool => PipelineEntryMilestoneFeature::active() && filled($record->project_milestone_id))
                    ->authorize(fn (PipelineEntry $record): bool => auth()->user()->can('update', $record->milestone))
                    ->action(function (PipelineEntry $record, array $data): void {
                        $record->milestone?->update($data);

                        $this->dispatch('projectMilestonesUpdated');
                        $this->dispatch('projectPipelineUpdated');
                    }),
                Action::make('deleteMilestone')
                    ->label('Delete Milestone')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PipelineEntry $record): bool => PipelineEntryMilestoneFeature::active() && filled($record->project_milestone_id))
                    ->authorize(fn (PipelineEntry $record): bool => auth()->user()->can('delete', $record->milestone))
                    ->action(function (PipelineEntry $record): void {
                        $milestone = $record->milestone;

                        if (! $milestone instanceof ProjectMilestone) {
                            return;
                        }

                        $milestone->pipelineEntries()->update(['project_milestone_id' => null]);
                        $milestone->delete();

                        $this->dispatch('projectMilestonesUpdated');
                        $this->dispatch('projectPipelineUpdated');
                    }),
            ])
            ->emptyStateHeading($pipeline ? 'No pipeline tasks' : 'No pipeline selected')
            ->emptyStateDescription(
                $pipeline
                    ? 'Create a task to start tracking work in this pipeline.'
                    : 'Create a pipeline to start tracking project work.'
            )
            ->emptyStateActions(
                $pipeline
                    ? [
                        CreateAction::make('createEntryFromEmptyState')
                            ->label('Add Pipeline Task')
                            ->modalHeading('Create Pipeline Task')
                            ->icon('heroicon-m-plus')
                            ->slideOver()
                            ->model(PipelineEntry::class)
                            ->schema($this->entryFormSchema($pipeline))
                            ->authorize(fn (): bool => auth()->user()->can('update', $this->record))
                            ->after(fn () => $this->dispatch('projectPipelineUpdated')),
                    ]
                    : [
                        Action::make('createPipelineFromEmptyState')
                            ->label('Add Pipeline')
                            ->icon('heroicon-m-plus')
                            ->slideOver()
                            ->schema($this->pipelineFormSchema())
                            ->action(fn (array $data) => $this->persistPipeline($data))
                            ->authorize(fn (): bool => auth()->user()->can('create', [Pipeline::class, $this->record])),
                    ],
            )
            ->headerActions([
                Action::make('kanban')
                    ->label('Kanban')
                    ->icon('heroicon-m-view-columns')
                    ->url(fn (): string => PipelineResource::getUrl('entries', [
                        'record' => $pipeline?->getKey(),
                        'project' => $this->record->getKey(),
                    ]))
                    ->visible(fn (): bool => (bool) $pipeline),
                CreateAction::make('createEntry')
                    ->label('Task')
                    ->modalHeading('Create Pipeline Task')
                    ->icon('heroicon-m-plus')
                    ->slideOver()
                    ->visible(fn (): bool => (bool) $pipeline?->entries()->exists())
                    ->model(PipelineEntry::class)
                    ->schema($this->entryFormSchema($pipeline))
                    ->authorize(fn (): bool => auth()->user()->can('update', $this->record))
                    ->after(fn () => $this->dispatch('projectPipelineUpdated')),
                CreateProjectMilestoneAction::make($this->record, 'createMilestone')
                    ->visible(fn (): bool => PipelineEntryMilestoneFeature::active()),
            ]);
    }

    public function openPipelineEntry(PipelineEntry $record): void
    {
        $entry = $this->resolvePipelineEntry($record->getKey());

        $this->mountAction(
            auth()->user()->can('update', $this->record) ? 'editPipelineEntry' : 'viewPipelineEntry',
            ['entry' => $entry->getKey()],
        );
    }

    public function viewPipelineEntryAction(): Action
    {
        return ViewPipelineEntryAction::make('viewPipelineEntry')
            ->authorize(fn (): bool => auth()->user()->can('view', $this->record))
            ->record(fn (array $arguments): PipelineEntry => $this->resolvePipelineEntry($arguments['entry'] ?? null));
    }

    public function editPipelineEntryAction(): Action
    {
        return EditPipelineEntryAction::make(
            $this->getSelectedPipeline(),
            'editPipelineEntry',
            after: fn () => $this->dispatch('projectPipelineUpdated'),
        )
            ->authorize(fn (): bool => auth()->user()->can('update', $this->record))
            ->record(fn (array $arguments): PipelineEntry => $this->resolvePipelineEntry($arguments['entry'] ?? null));
    }

    public function createPipelineAction(): Action
    {
        return Action::make('createPipeline')
            ->label('Create Pipeline')
            ->modalHeading('Create Pipeline')
            ->modalSubmitActionLabel('Create')
            ->slideOver()
            ->schema($this->pipelineFormSchema())
            ->action(fn (array $data) => $this->persistPipeline($data))
            ->authorize(fn (): bool => auth()->user()->can('create', [Pipeline::class, $this->record]));
    }

    protected function getPipelineSwitcherProjectId(): ?string
    {
        return (string) $this->record->getKey();
    }

    protected function getPipelineSwitcherCurrentPipelineId(): ?string
    {
        return $this->selectedPipelineId;
    }

    protected function onPipelineSwitcherSelected(string $pipelineId): void
    {
        $this->selectedPipelineId = $pipelineId;
        $this->resetTable();
    }

    protected function onPipelineSwitcherCleared(): void
    {
        $this->selectedPipelineId = null;
        $this->resetTable();
    }

    protected function getTableHeadingView(?Pipeline $pipeline): View
    {
        return view('project::filament.resources.projects.widgets.project-work-pipeline-heading', [
            'pipeline' => $pipeline,
        ]);
    }

    protected function milestoneProgressDescription(PipelineEntry $record, ?Pipeline $pipeline): string
    {
        if (! $pipeline || blank($record->project_milestone_id)) {
            return '';
        }

        $entries = PipelineEntry::query()
            ->where('project_milestone_id', $record->project_milestone_id)
            ->whereHas('pipelineStage', fn (Builder $query) => $query->where('pipeline_id', $pipeline->getKey()));

        $total = $entries->count();

        if ($total === 0) {
            return 'Progress: 0%';
        }

        $completed = (clone $entries)
            ->whereHas('pipelineStage', fn (Builder $query) => $query->where('classification', PipelineStageClassification::Complete))
            ->count();

        return 'Progress: ' . (int) round(($completed / $total) * 100) . '%';
    }

    /**
     * @return array<int, Component>
     */
    protected function entryFormSchema(?Pipeline $pipeline): array
    {
        return PipelineEntryForm::components($pipeline);
    }

    protected function resolvePipelineEntry(?string $entryId): PipelineEntry
    {
        $pipeline = $this->getSelectedPipeline();

        if ($pipeline === null) {
            abort(404);
        }

        return $pipeline
            ->entries()
            ->when(
                PipelineArchivingFeature::active(),
                fn (Builder $query): Builder => $query->withoutArchived(),
            )
            ->whereKey($entryId)
            ->firstOrFail();
    }

    /**
     * @return array<int, Component>
     */
    protected function pipelineFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->required()
                ->maxLength(65535),
            Repeater::make('stages')
                ->table([
                    TableColumn::make('Stage Name'),
                    TableColumn::make('Classification'),
                ])
                ->schema([
                    TextInput::make('name')
                        ->label('Stage')
                        ->distinct()
                        ->required(),
                    Select::make('classification')
                        ->label('Classification')
                        ->options(PipelineStageClassification::class)
                        ->enum(PipelineStageClassification::class)
                        ->required()
                        ->native()
                        ->default(PipelineStageClassification::Planning->value),
                ])
                ->default(
                    collect(PipelineStageClassification::cases())->map(fn (PipelineStageClassification $case): array => [
                        'name' => $case->getLabel(),
                        'classification' => $case->value,
                    ])->all()
                )
                ->reorderable()
                ->columnSpanFull()
                ->label('Pipeline Stages')
                ->minItems(1)
                ->maxItems(5),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function persistPipeline(array $data): void
    {
        $stages = $data['stages'] ?? [];

        /** @var Pipeline $pipeline */
        $pipeline = $this->record->pipelines()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $order = 1;

        foreach ($stages as $stage) {
            $pipeline->stages()->create([
                'name' => $stage['name'],
                'classification' => $stage['classification'] ?? null,
                'order' => $order++,
            ]);
        }

        $this->selectedPipelineId = (string) $pipeline->getKey();

        $this->resetTable();

        Notification::make()
            ->success()
            ->title('Pipeline created')
            ->send();
    }
}
