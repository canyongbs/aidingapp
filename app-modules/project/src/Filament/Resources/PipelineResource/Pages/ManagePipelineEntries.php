<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\Project;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class ManagePipelineEntries extends ManageRelatedRecords
{
    protected static string $resource = PipelineResource::class;

    protected static string $relationship = 'entries';

    public ?string $viewType = 'null';

    #[Locked, Url]
    public ?string $project = null;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?string $title = 'Pipeline Entries List';

    protected static string $view = 'project::filament.pages.manage-pipeline-entries';

    protected static ?string $navigationLabel = 'Pipeline Entries';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $ownerRecord = $this->getRecord();

        assert($ownerRecord instanceof Pipeline);
        $this->viewType = session('pipeline-view-type') ?? 'table';
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        assert($record instanceof Pipeline);

        return 'Manage Pipeline Entries';
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        assert($ownerRecord instanceof Pipeline);

        return [$item];
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();
        throw_if(! $record instanceof Pipeline, new Exception('Pipeline not found.'));

        $project = Project::find($this->project);

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            '#' => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? ['##' => $breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
        session(['pipeline-view-type' => $viewType]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $pipeline = $this->getOwnerRecord();
        assert($pipeline instanceof Pipeline);

        return $table
            ->columns([
                TextColumn::make('pipelineStage.name')
                    ->label('Stage')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('stage')
                    ->relationship('pipelineStage', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Select::make('stage_id')
                            ->label('Stage')
                            ->relationship('pipelineStage', 'name', fn (Builder $query) => $query->where('pipeline_id', $pipeline->id))
                            ->required(),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
