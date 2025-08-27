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

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Table;
use AidingApp\Task\Models\Task;
use Filament\Tables\Filters\Filter;
use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Contact\Models\Contact;
use Filament\Forms\Components\Select;
use App\Models\Scopes\EducatableSearch;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use App\Features\ConfidentialTaskFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\Task\Filament\Concerns\TaskEditForm;
use Filament\Resources\Pages\ManageRelatedRecords;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Task\Filament\Concerns\TaskViewActionInfoList;
use AidingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

class ManageTasks extends ManageRelatedRecords
{
    use TaskEditForm;
    use TaskViewActionInfoList;

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
                IdColumn::make(),
                TextColumn::make('title')
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
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                    ->hidden(function (Table $table) {
                        return $table->getFilter('my_tasks')->getState()['isActive'] ?? false;
                    }),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'concern', search: $search)))
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Contact::class => ContactResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
            ])->filters([
                Filter::make('my_tasks')
                    ->label('My Tasks')
                    ->query(
                        fn (Builder $query) => $query->where('assigned_to', auth()->id())
                    )
                    ->form([
                        Checkbox::make('isActive')
                            ->label('My Tasks')
                            ->afterStateUpdated(fn (Set $set, $state) => $state ? $set('../my_teams_tasks.isActive', false) : null)
                            ->default(true),
                    ]),
                Filter::make('my_teams_tasks')
                    ->label("My Team's Tasks")
                    ->query(
                        function (Builder $query) {
                            /** @var User $user */
                            $user = auth()->user();

                            $teamUserIds = $user->team->users()->get()->pluck('id');

                            return $query->whereIn('assigned_to', $teamUserIds)->get();
                        }
                    )
                    ->form([
                        Checkbox::make('isActive')
                            ->label("My Team's Tasks")
                            ->afterStateUpdated(function (Set $set, string $state) {
                                return $state ? $set('../my_tasks.isActive', false) : null;
                            }),
                    ]),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default([
                        TaskStatus::Pending->value,
                        TaskStatus::InProgress->value,
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', Task::class)
                    ->form([
                        Fieldset::make('Confidentiality')
                            ->visible(ConfidentialTaskFeature::active())
                            ->schema([
                                Checkbox::make('is_confidential')
                                    ->label('Confidential')
                                    ->live()
                                    ->columnSpanFull(),
                                Select::make('confidential_task_users')
                                    ->relationship('confidentialAccessUsers', 'name')
                                    ->preload()
                                    ->label('Users')
                                    ->multiple()
                                    ->exists('users', 'id')
                                    ->visible(fn (Get $get) => $get('is_confidential')),
                                Select::make('confidential_task_teams')
                                    ->relationship('confidentialAccessTeams', 'name')
                                    ->preload()
                                    ->label('Teams')
                                    ->multiple()
                                    ->exists('teams', 'id')
                                    ->visible(fn (Get $get) => $get('is_confidential')),
                            ]),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(100)
                            ->string(),
                        Textarea::make('description')
                            ->required()
                            ->string(),
                        DateTimePicker::make('due')
                            ->label('Due Date')
                            ->native(false),
                        Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'name', $this->scopeAssignmentRelationshipBasedOnConcern())
                            ->nullable()
                            ->searchable(['name', 'email'])
                            ->default(auth()->id()),
                        Select::make('concern_id')
                            ->label('Related To')
                            ->relationship('concern', 'first_name')
                            ->nullable()
                            ->afterStateUpdated($this->updateAssignmentAfterConcernSelected()),
                    ])
                    ->modalHeading('Create Task')
                    ->modalSubmitActionLabel('Create Task'),
            ])
            ->actions([
                TaskViewAction::make()
                    ->authorize('view', Task::class),
                EditAction::make()
                    ->form(fn () => $this->editFormFields())
                    ->authorize('update', Task::class),
                DeleteAction::make()
                    ->authorize('delete', Task::class),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return $user->can(['task.view-any', 'task.*.view']) && parent::canAccess($arguments);
    }
}
