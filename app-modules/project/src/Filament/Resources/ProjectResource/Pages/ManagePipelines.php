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

namespace AidingApp\Project\Filament\Resources\ProjectResource\Pages;

use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManagePipelines extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'pipelines';

    public static function getNavigationLabel(): string
    {
        return 'Pipelines';
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', [Pipeline::class, $arguments['record']]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Repeater::make('stages')
                    ->relationship('stages')
                    ->schema([
                        TextInput::make('name')
                            ->label('Stage')
                            ->distinct()
                            ->required(),
                    ])
                    ->orderColumn('order')
                    ->reorderable()
                    ->columnSpanFull()
                    ->label('Pipeline Stages')
                    ->minItems(1)
                    ->maxItems(5),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('createdBy.name')->label('Created By'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(PipelineResource::getUrl('create', ['project' => $this->getRecord()->getKey()]))
                    ->authorize(fn (): bool => auth()->user()->can('create', Pipeline::class) && auth()->user()->can('update', $this->getRecord())),
            ])
            ->filters([
                Filter::make('createdBy')
                    ->label('My Pipelines')
                    ->default()
                    ->query(fn (Builder $query) => $query->where('created_by_id', auth()->id())),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
