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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

class AssignedToRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedTo';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.full')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('user.name')
                    ->label('Name'),
            ])
            ->paginated(false)
            ->headerActions([
                Action::make('assign-to-me')
                    ->visible(fn () => auth()->user()->can('update', $this->getOwnerRecord()) && is_null($this->getOwnerRecord()->assignedTo) && in_array(auth()->user()?->getKey(), $this->getOwnerRecord()->priority->type->managers
                        ->flatMap(fn ($managers) => $managers->users)
                        ->pluck('id')
                        ->toArray()))
                    ->label('Assign To Me')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (array $data) => $this->getOwnerRecord()->assignments()->create([
                        'user_id' => auth()->user()?->getKey(),
                        'assigned_by_id' => auth()->user()->getKey() ?? null,
                        'assigned_at' => now(),
                        'status' => ServiceRequestAssignmentStatus::Active,
                    ])),
                Action::make('assign-service-request')
                    ->visible(fn () => auth()->user()->can('update', $this->getOwnerRecord()))
                    ->label(fn () => $this->getOwnerRecord()->assignedTo ? 'Reassign' : 'Assign')
                    ->color('gray')
                    ->action(fn (array $data) => $this->getOwnerRecord()->assignments()->create([
                        'user_id' => $data['userId'],
                        'assigned_by_id' => auth()->user()->getKey() ?? null,
                        'assigned_at' => now(),
                        'status' => ServiceRequestAssignmentStatus::Active,
                    ]))
                    ->schema([
                        Select::make('userId')
                            ->label(fn () => $this->getOwnerRecord()->assignedTo ? 'Reassign' : 'Assign')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => User::query()
                                ->where(new Expression('lower(name)'), 'like', '%' . str($search)->lower() . '%')
                                ->whereHas('team.manageableServiceRequestTypes', function (Builder $query) {
                                    $query->where('service_request_type_id', $this->getOwnerRecord()?->priority->type_id ?? null);
                                })
                                ->where('id', '!=', $this->getOwnerRecord()->assignedTo?->user_id)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                            ->placeholder('Search for and select a User')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (ServiceRequestAssignment $assignment) => UserResource::getUrl('view', ['record' => $assignment->user])),
            ]);
    }

    public function getOwnerRecord(): ServiceRequest
    {
        /** @var ServiceRequest $record */
        $record = parent::getOwnerRecord();

        return $record;
    }
}
