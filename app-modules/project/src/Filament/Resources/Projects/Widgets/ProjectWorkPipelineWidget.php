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
use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Filament\Tables\ProjectPipelinesTable;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TableSelect;
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
    #[Locked]
    public Project $record;

    public ?string $selectedPipelineId = null;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-work-pipeline-widget';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', Pipeline::class);
    }

    public function mount(): void
    {
        $this->selectedPipelineId = $this->record
            ->pipelines()
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
                    ->with(['milestones', 'assets', 'serviceRequests', 'pipelineStage.pipeline.project']);
            })
            ->heading(fn (): View => $this->getTableHeadingView($pipeline))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(['pipeline_entries.name'])
                    ->sortable(),
                ViewColumn::make('milestones')
                    ->label('Milestones')
                    ->view('project::filament.tables.columns.pipeline-entry.milestones'),
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
            ->defaultGroup(
                Group::make('pipelineStage.name')
                    ->label('Stage')
                    ->collapsible(),
            )
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->schema($this->entryFormSchema($pipeline))
                    ->authorize(fn (): bool => auth()->user()->can('update', $this->record)),
            ])
            ->emptyStateHeading($pipeline ? 'No pipeline entries' : 'No pipeline selected')
            ->emptyStateDescription(
                $pipeline
                    ? 'Create an entry to start tracking work in this pipeline.'
                    : 'Create a pipeline to start tracking project work.'
            )
            ->emptyStateActions(
                $pipeline
                    ? [
                        CreateAction::make('createEntryFromEmptyState')
                            ->label('Add Pipeline Entry')
                            ->icon('heroicon-m-plus')
                            ->slideOver()
                            ->model(PipelineEntry::class)
                            ->schema($this->entryFormSchema($pipeline))
                            ->authorize(fn (): bool => auth()->user()->can('update', $this->record)),
                    ]
                    : [
                        Action::make('createPipelineFromEmptyState')
                            ->label('Add Pipeline')
                            ->icon('heroicon-m-plus')
                            ->slideOver()
                            ->schema($this->pipelineFormSchema())
                            ->action(fn (array $data) => $this->persistPipeline($data))
                            ->authorize(fn (): bool => auth()->user()->can('create', Pipeline::class) && auth()->user()->can('update', $this->record)),
                    ],
            )
            ->headerActions([
                CreateAction::make('createEntry')
                    ->label('New Pipeline Entry')
                    ->icon('heroicon-m-plus')
                    ->slideOver()
                    ->visible(fn (): bool => (bool) $pipeline?->entries()->exists())
                    ->model(PipelineEntry::class)
                    ->schema($this->entryFormSchema($pipeline))
                    ->authorize(fn (): bool => auth()->user()->can('update', $this->record)),
            ]);
    }

    public function selectPipelineAction(): Action
    {
        return Action::make('selectPipeline')
            ->label('Select Pipeline')
            ->modalHeading('Select Pipeline')
            ->modalSubmitActionLabel('Select')
            ->fillForm(fn (): array => ['pipeline_id' => $this->selectedPipelineId])
            ->schema([
                TableSelect::make('pipeline_id')
                    ->hiddenLabel()
                    ->tableConfiguration(ProjectPipelinesTable::class)
                    ->tableArguments(['projectId' => $this->record->getKey()])
                    ->required(),
            ])
            ->action(function (array $data): void {
                $pipelineId = $data['pipeline_id'];

                if (blank($pipelineId) || ! $this->record->pipelines()->whereKey($pipelineId)->exists()) {
                    Notification::make()
                        ->danger()
                        ->title('Invalid pipeline selection')
                        ->body('The selected pipeline does not belong to this project.')
                        ->send();

                    return;
                }

                $this->selectedPipelineId = (string) $pipelineId;
                $this->resetTable();
            });
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
            ->authorize(fn (): bool => auth()->user()->can('create', Pipeline::class) && auth()->user()->can('update', $this->record));
    }

    protected function getTableHeadingView(?Pipeline $pipeline): View
    {
        return view('project::filament.resources.projects.widgets.project-work-pipeline-heading', [
            'pipeline' => $pipeline,
        ]);
    }

    /**
     * @return array<int, Component>
     */
    protected function entryFormSchema(?Pipeline $pipeline): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('pipeline_stage_id')
                ->label('Stage')
                ->options(fn (): array => $pipeline
                    ? $pipeline->stages()->orderBy('order')->pluck('name', 'id')->all()
                    : [])
                ->required(),
            ...PipelineEntryForm::components($pipeline),
        ];
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
