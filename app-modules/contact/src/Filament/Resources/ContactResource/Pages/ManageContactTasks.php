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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Task\Filament\RelationManagers\BaseTaskRelationManager;
use AidingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;
use AidingApp\Task\Models\Task;
use App\Features\ConfidentialTaskFeature;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class ManageContactTasks extends BaseTaskRelationManager
{
    protected static string $resource = ContactResource::class;

    protected static string $relationship = 'tasks';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Tasks';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Tasks';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(50)
                    ->icon(fn ($record) => ConfidentialTaskFeature::active() && $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn ($record) => ConfidentialTaskFeature::active() && $record->is_confidential ? 'Confidential' : null),
                TextColumn::make('status')
                    ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                    ->badge(),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null),
                TextColumn::make('concern.full')
                    ->label('Related To')
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Contact::class => ContactResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
            ])
            ->filters([
                Filter::make('my_tasks')
                    ->label('My Tasks')
                    ->query(
                        fn ($query) => $query->where('assigned_to', auth()->id())
                    ),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name',
                        fn (Builder $query) => $query->tap(new HasLicense($this->getOwnerRecord()->getLicenseType())),
                    )
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default(
                        [
                            TaskStatus::Pending->value,
                            TaskStatus::InProgress->value,
                        ]
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $data = collect($data);

                        /** @var Task $task */
                        $task = new ($model)($data->except('assigned_to')->toArray());

                        $task->assigned_to = $data->get('assigned_to');

                        $task->concern()->associate($this->getOwnerRecord());

                        $task->save();

                        return $task;
                    }),
            ])
            ->actions([
                TaskViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordUrl(null);
    }
}
