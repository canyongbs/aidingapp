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

namespace AidingApp\ServiceManagement\Filament\Resources\SlaResource\RelationManagers;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ServiceRequestPrioritiesRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceRequestPriorities';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->inverseRelationship('sla')
            ->columns([
                IdColumn::make(),
                TextColumn::make('type.name')
                    ->url(fn (ServiceRequestPriority $record): string => ServiceRequestTypeResource::getUrl('edit', ['record' => $record->type]))
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
                TextColumn::make('service_requests_count')
                    ->label('# of Service Requests')
                    ->counts('serviceRequests')
                    ->sortable(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query, AssociateAction $action) => $query->where('type_id', Arr::last($this->mountedTableActionsData)['type_id'] ?? null)->orderBy('order'))
                    ->preloadRecordSelect()
                    ->form(fn (AssociateAction $action): array => [
                        Select::make('type_id')
                            ->relationship(
                                'type',
                                'name',
                                fn (Builder $query) => $query->whereRelation('priorities', 'sla_id', null),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        $action->getRecordSelect()
                            ->visible(fn (Get $get): bool => filled($get('type_id'))),
                    ]),
            ])
            ->actions([
                DissociateAction::make(),
            ])
            ->groupedBulkActions([
                DissociateBulkAction::make(),
            ]);
    }
}
