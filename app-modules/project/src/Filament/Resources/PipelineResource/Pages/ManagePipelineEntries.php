<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use Dom\Text;
use Exception;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\RawJs;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
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

        return "Manage Pipeline Entries";
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
                // CreateAction::make()
                //     ->form([
                //         Select::make('stage_id')
                //             ->label('Stage')
                //             ->relationship('stage', 'name', fn (Builder $query) => $query->where('pipeline_id', $this->getOwnerRecord()->id))
                //             ->required(),
                //         MorphToSelect::make('entryable')
                //             ->label('Entry')
                //             ->required()
                //             ->types([
                //                 Type::make(Contact::class)
                //                     ->titleAttribute('full_name'),
                //                 Type::make(Project::class)
                //                     ->titleAttribute('name'),
                //             ]),
                //     ]),
            ])
            ->defaultSort('created_at', 'desc');
    }   
}
