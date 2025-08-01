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

namespace AidingApp\Project\Filament\Resources\ProjectResource\Pages;

use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Task\Models\Task;
use App\Features\ConfidentialTaskFeature;
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
                    ->searchable()
                    ->sortable()
                    ->icon(fn ($record) => ConfidentialTaskFeature::active() && $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn ($record) => ConfidentialTaskFeature::active() && $record->is_confidential ? 'Confidential' : null),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->whereNull('project_id')
                    )
                    ->preloadRecordSelect()
                    ->authorize('updateAny', Task::class),
            ])
            ->actions([
                DissociateAction::make()
                    ->authorize('updateAny', Task::class),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->authorize('updateAny', Task::class),
                ]),
            ]);
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return $user->can(['task.view-any', 'task.*.view']) && parent::canAccess($arguments);
    }
}
