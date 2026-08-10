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

use AidingApp\Project\Filament\Concerns\HasPipelineSwitcherAction;
use AidingApp\Project\Filament\Resources\Pipelines\Actions\EditPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Actions\ViewPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Filament\Resources\Pipelines\Resources\PipelineEntries\PipelineEntryResource;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ManagePipelineEntries extends ManageRelatedRecords
{
    use HasPipelineSwitcherAction;

    protected static string $resource = PipelineResource::class;

    protected static ?string $relatedResource = PipelineEntryResource::class;

    protected static string $relationship = 'entries';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?string $title = 'Manage Pipeline Tasks';

    protected static ?string $breadcrumb = 'Pipeline Tasks';

    protected string $view = 'project::filament.pages.manage-pipeline-entries';

    protected static ?string $navigationLabel = 'Pipeline Tasks';

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString(
            view('project::filament.pages.back-to-project', [
                'url' => ProjectResource::getUrl('view', ['record' => $this->getParentRecord()]),
            ])->render(),
        );
    }

    public function table(Table $table): Table
    {
        $pipeline = $this->getOwnerRecord();
        assert($pipeline instanceof Pipeline);

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(['pipeline_entries.name']),
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
            ->recordActions([
                ViewPipelineEntryAction::make(),
                EditPipelineEntryAction::make($pipeline),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('pipeline_stage_id')
                            ->label('Stage')
                            ->relationship('pipelineStage', 'name', fn (Builder $query) => $query->where('pipeline_id', $pipeline->id))
                            ->required(),
                        ...PipelineEntryForm::components($pipeline),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getPipelineSwitcherProjectId(): ?string
    {
        $project = $this->getParentRecord();

        return $project ? (string) $project->getKey() : null;
    }

    protected function getPipelineSwitcherCurrentPipelineId(): ?string
    {
        return (string) $this->getOwnerRecord()->getKey();
    }

    protected function onPipelineSwitcherSelected(string $pipelineId): void
    {
        $this->redirect(static::getUrl([
            'record' => $pipelineId,
            'project' => $this->getParentRecord(),
        ]));
    }
}
