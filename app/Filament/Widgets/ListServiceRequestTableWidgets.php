<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Widgets;

use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\EducatableSearch;
use App\Models\Scopes\EducatableSort;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ListServiceRequestTableWidgets extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceRequest::with([
                    'latestInboundServiceRequestUpdate',
                    'latestOutboundServiceRequestUpdate',
                    'priority' => [
                        'sla',
                    ],
                    'status',
                ])
            )
            ->columns([
                IdColumn::make(),
                TextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable()
                    ->sortable()
                    ->url(fn (ServiceRequest $record) => ServiceRequestResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('status.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('respondent.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->{$record->respondent::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'respondent', search: $search)))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->tap(new EducatableSort($direction))),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('response_sla_compliance')
                    ->label('SLA Response')
                    ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResponseSlaComplianceStatus())
                    ->tooltip(fn (ServiceRequest $record): ?string => $record->getResponseSlaComplianceStatus()?->getLabel()),
                IconColumn::make('resolution_sla_compliance')
                    ->label('SLA Resolution')
                    ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResolutionSlaComplianceStatus())
                    ->tooltip(fn (ServiceRequest $record): ?string => $record->getResolutionSlaComplianceStatus()?->getLabel()),
                TextColumn::make('created_at')->date('Y-m-d'),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->relationship('priority', 'name', fn (Builder $query) => $query->with('type')->whereRelation('type', 'deleted_at'))
                    ->getOptionLabelFromRecordUsing(fn (ServiceRequestPriority $record) => "{$record->type->name} - {$record->name}")
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Action::make('View')->url(function (ServiceRequest $serviceRequest) {
                    return ServiceRequestResource::getUrl('view', ['record' => $serviceRequest]);
                }),
                Action::make('Edit')->url(function (ServiceRequest $serviceRequest) {
                    return ServiceRequestResource::getUrl('edit', ['record' => $serviceRequest]);
                }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    protected function getTableHeading(): string
    {
        return 'Latest Service Requests';
    }
}
