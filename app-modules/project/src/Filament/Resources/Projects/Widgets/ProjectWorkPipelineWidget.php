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
use AidingApp\Project\Models\ProjectMilestone;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;

class ProjectWorkPipelineWidget extends TableWidget
{
    use HasPipelineSwitcherAction;

    #[Locked]
    public Project $record;

    public ?string $selectedPipelineId = null;

    /** @var array<string, int> */
    protected array $milestoneProgressPercentages = [];

    protected ?string $milestoneProgressPercentagesLoadedForPipelineId = null;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-work-pipeline-widget';

    public function mount(): void
    {
        $this->selectedPipelineId = $this->record
            ->pipelines()
            ->withoutArchived()
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
            ->withoutArchived()
            ->whereKey($this->selectedPipelineId)
            ->first();
    }

    public function table(Table $table): Table
    {
        $pipeline = $this->getSelectedPipeline();

        $canManageMilestones = auth()->user()->can('update', $this->record);

        return $table
            ->query(fn (): Builder => $this->getPipelineEntriesQuery($pipeline))
            ->heading(fn (): View => $this->getTableHeadingView($pipeline))
            ->columns([
                TextColumn::make('name')
                    ->label('Task Name')
                    ->state(fn (PipelineEntry $record): string => $this->isPlaceholderRecord($record) ? 'No tasks yet' : $record->name)
                    ->color(fn (PipelineEntry $record): ?string => $this->isPlaceholderRecord($record) ? 'gray' : null)
                    ->searchable(['pipeline_entries.name'])
                    ->sortable()
                    ->extraAttributes(fn (PipelineEntry $record): array => $this->isPlaceholderRecord($record) ? [] : ['class' => 'underline'])
                    ->disabledClick(fn (PipelineEntry $record): bool => $this->isPlaceholderRecord($record))
                    ->action(fn (PipelineEntry $record) => $this->openPipelineEntry($record)),

                TextColumn::make('pipelineStage.name')
                    ->label('Stage')
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
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->placeholder('N/A')
                    ->sortable(),
                TextColumn::make('due')
                    ->label('Target Date')
                    ->date()
                    ->placeholder('N/A')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('N/A'),
            ])
            ->filters([
                SelectFilter::make('classification')
                    ->label('Statuses')
                    ->options(PipelineStageClassification::class)
                    ->multiple()
                    ->default(
                        collect(PipelineStageClassification::cases())
                            ->reject(fn (PipelineStageClassification $case): bool => $case === PipelineStageClassification::Complete)
                            ->map(fn (PipelineStageClassification $case): string => $case->value)
                            ->all()
                    )
                    ->query(function (Builder $query, array $data) use ($pipeline): Builder {
                        $includePlaceholders = $pipeline !== null;

                        return $query->when(
                            filled($data['values'] ?? null),
                            fn (Builder $query): Builder => $query->where(function (Builder $query) use ($data, $includePlaceholders): void {
                                $query->whereHas(
                                    'pipelineStage',
                                    fn (Builder $query): Builder => $query->whereIn('classification', $data['values']),
                                )->when(
                                    $includePlaceholders,
                                    fn (Builder $query): Builder => $query->orWhere('is_placeholder', 1),
                                );
                            }),
                        );
                    }),
            ])
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(50)
            ->defaultGroup(
                function () use ($pipeline, $canManageMilestones) {
                    return Group::make('milestone.title')
                        ->label('Milestone')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible()
                        ->orderQueryUsing(function (Builder $query, string $direction): Builder {
                            $model = $query->getModel();

                            return $query
                                ->orderByRaw("{$model->qualifyColumn('project_milestone_id')} IS NULL")
                                ->orderBy(
                                    ProjectMilestone::query()
                                        ->select('title')
                                        ->whereColumn(
                                            (new ProjectMilestone())->qualifyColumn('id'),
                                            $model->qualifyColumn('project_milestone_id'),
                                        ),
                                    $direction,
                                );
                        })
                        ->getTitleFromRecordUsing(
                            fn (PipelineEntry $record): Htmlable => new HtmlString(
                                trim(view('project::filament.tables.groups.milestone-title', [
                                    'milestone' => $record->milestone,
                                ])->render())
                            )
                        )
                        ->getDescriptionFromRecordUsing(
                            fn (PipelineEntry $record): ?View => filled($record->project_milestone_id)
                                ? view('project::filament.tables.groups.milestone', [
                                    'milestone' => $record->milestone,
                                    'canManageMilestone' => $canManageMilestones,
                                    'percentage' => $this->milestoneProgressPercentage($record, $pipeline),
                                ])
                                : null
                        );
                }
            )
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
                            ->after(function (): void {
                                $this->resetMilestoneProgressPercentages();
                                $this->dispatch('projectPipelineUpdated');
                            }),
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
                    ->after(function (): void {
                        $this->resetMilestoneProgressPercentages();
                        $this->dispatch('projectPipelineUpdated');
                    }),
                CreateProjectMilestoneAction::make($this->record, 'createMilestone'),
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
            after: function (): void {
                $this->resetMilestoneProgressPercentages();
                $this->dispatch('projectPipelineUpdated');
            },
            afterArchive: function (): void {
                $this->resetMilestoneProgressPercentages();
                $this->resetTable();
                $this->dispatch('projectPipelineUpdated');
            },
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

    public function manageMilestoneAction(): Action
    {
        return Action::make('manageMilestone')
            ->slideOver()
            ->modalHeading(function (Action $action): string {
                $milestone = $this->getActionMilestone($action);

                return $milestone instanceof ProjectMilestone ? $milestone->title : 'Milestone';
            })
            ->fillForm(function (Action $action): array {
                $milestone = $this->getActionMilestone($action);

                return $milestone?->attributesToArray() ?? [];
            })
            ->schema(CreateProjectMilestoneAction::formSchema())
            ->authorize(function (Action $action): bool {
                $milestone = $this->getActionMilestone($action);

                return $milestone instanceof ProjectMilestone && auth()->user()->can('update', $milestone);
            })
            ->action(function (Action $action, array $data): void {
                $milestone = $this->getActionMilestone($action);

                if (! $milestone instanceof ProjectMilestone) {
                    return;
                }

                $milestone->update($data);

                $this->resetMilestoneProgressPercentages();
                $this->dispatch('projectMilestonesUpdated');
                $this->dispatch('projectPipelineUpdated');
            })
            ->extraModalFooterActions(fn (Action $action): array => [
                Action::make('deleteMilestone')
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function () use ($action): bool {
                        $milestone = $this->getActionMilestone($action);

                        return $milestone instanceof ProjectMilestone && auth()->user()->can('delete', $milestone);
                    })
                    ->action(function () use ($action): void {
                        $milestone = $this->getActionMilestone($action);

                        if (! $milestone instanceof ProjectMilestone) {
                            return;
                        }

                        $milestone->pipelineEntries()->update(['project_milestone_id' => null]);
                        $milestone->delete();

                        $this->resetMilestoneProgressPercentages();
                        $this->resetTable();
                        $this->dispatch('projectMilestonesUpdated');
                        $this->dispatch('projectPipelineUpdated');
                    })
                    ->cancelParentActions(),
            ]);
    }

    /**
     * @return Builder<PipelineEntry>
     */
    protected function getPipelineEntriesQuery(?Pipeline $pipeline): Builder
    {
        if (! $pipeline) {
            return PipelineEntry::query()->whereRaw('1 = 0');
        }

        $eagerLoad = [
            'assets',
            'serviceRequests',
            'pipelineStage.pipeline.project',
            'milestone',
        ];

        // Filament only renders a group header when at least one row belongs to it. To keep
        // milestones without any tasks visible, we union a synthetic placeholder row (flagged
        // via "is_placeholder") for every milestone that currently has no pipeline entries.
        return PipelineEntry::query()
            ->fromSub($this->buildMilestoneGroupedSubquery($pipeline), 'pipeline_entries')
            ->with($eagerLoad);
    }

    /**
     * @return Builder<PipelineEntry>
     */
    protected function buildMilestoneGroupedSubquery(Pipeline $pipeline): Builder
    {
        // Because the outer query is built from this subquery via fromSub(), every
        // PipelineEntry is hydrated with only these attributes: any attribute used by a
        // table column or closure but omitted here will silently resolve to null instead
        // of raising an error, so this list must include every attribute the table uses.
        $columns = ['id', 'name', 'pipeline_stage_id', 'project_milestone_id', 'is_visible_to_guests', 'start_date', 'due', 'created_by'];

        $entries = PipelineEntry::query()
            ->whereHas('pipelineStage', fn (Builder $query): Builder => $query->where('pipeline_id', $pipeline->getKey()))
            ->withoutArchived()
            ->select(array_map(fn (string $column): string => "pipeline_entries.{$column}", $columns))
            ->selectRaw('0 as is_placeholder');

        $placeholderSelects = array_map(function (string $column): Expression {
            return match ($column) {
                'id', 'project_milestone_id' => new Expression("project_milestones.id as {$column}"),
                default => new Expression("null as {$column}"),
            };
        }, $columns);

        $placeholderSelects[] = new Expression('1 as is_placeholder');

        $emptyMilestones = PipelineEntry::query()->getConnection()
            ->table('project_milestones')
            ->where('project_milestones.project_id', $pipeline->project_id)
            ->whereNull('project_milestones.archived_at')
            ->whereNull('project_milestones.deleted_at')
            ->whereNotExists(function (QueryBuilder $query) use ($pipeline): void {
                $query->select(new Expression('1'))
                    ->from('pipeline_entries')
                    ->join('pipeline_stages', 'pipeline_stages.id', '=', 'pipeline_entries.pipeline_stage_id')
                    ->whereColumn('pipeline_entries.project_milestone_id', 'project_milestones.id')
                    ->where('pipeline_stages.pipeline_id', $pipeline->getKey())
                    ->whereNull('pipeline_entries.archived_at');
            })
            ->select($placeholderSelects);

        return $entries->unionAll($emptyMilestones);
    }

    protected function isPlaceholderRecord(PipelineEntry $record): bool
    {
        return ((int) ($record->getAttribute('is_placeholder') ?? 0)) === 1;
    }

    protected function getPipelineSwitcherProjectId(): ?string
    {
        return (string) $this->record->getKey();
    }

    protected function getActionMilestone(Action $action): ?ProjectMilestone
    {
        $milestoneId = $action->getArguments()['milestone'] ?? null;

        if (blank($milestoneId)) {
            return null;
        }

        return $this->record->milestones()->whereKey($milestoneId)->first();
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

    protected function milestoneProgressPercentage(PipelineEntry $record, ?Pipeline $pipeline): int
    {
        if (! $pipeline || blank($record->project_milestone_id)) {
            return 0;
        }

        $this->loadMilestoneProgressPercentages($pipeline);

        return $this->milestoneProgressPercentages["{$pipeline->getKey()}:{$record->project_milestone_id}"] ?? 0;
    }

    protected function resetMilestoneProgressPercentages(): void
    {
        $this->milestoneProgressPercentages = [];
        $this->milestoneProgressPercentagesLoadedForPipelineId = null;
    }

    /**
     * Precomputes the progress percentage for every milestone in the given pipeline
     * with a single aggregate query, rather than issuing 2 queries per unique milestone.
     */
    protected function loadMilestoneProgressPercentages(Pipeline $pipeline): void
    {
        if ($this->milestoneProgressPercentagesLoadedForPipelineId === $pipeline->getKey()) {
            return;
        }

        $counts = PipelineEntry::query()
            ->whereNotNull('project_milestone_id')
            ->whereHas('pipelineStage', fn (Builder $query) => $query->where('pipeline_id', $pipeline->getKey()))
            ->withoutArchived()
            ->join('pipeline_stages', 'pipeline_stages.id', '=', 'pipeline_entries.pipeline_stage_id')
            ->selectRaw('pipeline_entries.project_milestone_id')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where pipeline_stages.classification = ?) as completed', [PipelineStageClassification::Complete->value])
            ->groupBy('pipeline_entries.project_milestone_id')
            ->get();

        foreach ($counts as $count) {
            $attributes = $count->getAttributes();

            $cacheKey = "{$pipeline->getKey()}:{$attributes['project_milestone_id']}";

            $total = (int) $attributes['total'];
            $completed = (int) $attributes['completed'];

            $percentage = $total === 0
                ? 0
                : (int) round(($completed / $total) * 100);

            $this->milestoneProgressPercentages[$cacheKey] = $percentage;
        }

        $this->milestoneProgressPercentagesLoadedForPipelineId = $pipeline->getKey();
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
            ->withoutArchived()
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
