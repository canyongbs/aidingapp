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

namespace AidingApp\Project\Filament\Resources\ProjectResource\Pages;

use AidingApp\Project\Filament\Resources\ProjectResource;
use App\Features\ManageTasksFeature;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageTasks extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'tasks';

    public static function getNavigationLabel(): string
    {
        return 'Tasks';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->whereNull('project_id')
                    )
                    ->preloadRecordSelect(),
            ])
            ->actions([
                DissociateAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                ]),
            ]);
    }

    public static function canAccess(array $arguments = []): bool
    {
        return ManageTasksFeature::active() && parent::canAccess($arguments);
    }
}
