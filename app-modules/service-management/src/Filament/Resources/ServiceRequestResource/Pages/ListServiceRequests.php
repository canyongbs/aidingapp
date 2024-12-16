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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Tables\Table;
use App\Models\Authenticatable;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use AidingApp\Contact\Models\Contact;
use App\Models\Scopes\EducatableSort;
use App\Models\Scopes\EducatableSearch;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\Contact\Models\Organization;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;

class ListServiceRequests extends ListRecords
{
    protected ?string $heading = 'Service Management';

    protected static string $resource = ServiceRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'latestInboundServiceRequestUpdate',
                'latestOutboundServiceRequestUpdate',
                'priority' => [
                    'sla',
                ],
                'status',
            ])
                ->when(! auth()->user()->hasRole(Authenticatable::SUPER_ADMIN_ROLE), function (Builder $q) {
                    return $q->whereHas('priority.type.managers', function (Builder $query): void {
                        $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                    })->orWhereHas('priority.type.auditors', function (Builder $query): void {
                        $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                    });
                }))
            ->columns([
                IdColumn::make(),
                TextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable(['service_request_number', 'title'])
                    ->sortable()
                    ->description(fn (ServiceRequest $record): string => $record->title),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('respondent.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->{$record->respondent::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'respondent', search: $search)))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->tap(new EducatableSort($direction)))
                    ->toggleable(),
                TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned To')
                    ->badge(fn (ServiceRequest $record) => is_null($record->assignedTo))
                    ->searchable()
                    ->sortable()
                    ->default('Unassigned')
                    ->toggleable(),
                IconColumn::make('response_sla_compliance')
                    ->label('SLA Response')
                    ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResponseSlaComplianceStatus())
                    ->tooltip(fn (ServiceRequest $record): ?string => $record->getResponseSlaComplianceStatus()?->getLabel())
                    ->toggleable(),
                IconColumn::make('resolution_sla_compliance')
                    ->label('SLA Resolution')
                    ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResolutionSlaComplianceStatus())
                    ->tooltip(fn (ServiceRequest $record): ?string => $record->getResolutionSlaComplianceStatus()?->getLabel())
                    ->toggleable(),
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
                SelectFilter::make('organizations')
                    ->options(Organization::pluck('name', 'id')->toArray())
                    ->modifyQueryUsing(fn (Builder $query, $state): Builder => $query->when(
                        ! empty($state['value']),
                        fn (Builder $query) => $query->whereHasMorph(
                            'respondent',
                            [Contact::class],
                            fn (Builder $query): Builder => $query->whereRelation(
                                'organizations',
                                (new Organization())->getKeyName(),
                                $state['value']
                            )
                        )
                    ))
                    ->preload(),
                Filter::make('Unassigned')
                    ->query(
                        fn (Builder $query) => $query->whereDoesntHave('assignedTo', function (Builder $query) {
                            $query->where('status', ServiceRequestAssignmentStatus::Active);
                        })
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (ServiceRequest $record) => $record->status?->classification === SystemServiceRequestClassification::Closed ? false : true),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $deletedRecordsCount = ServiceRequest::query()
                                ->whereKey($records)
                                ->when(! auth()->user()->hasRole(Authenticatable::SUPER_ADMIN_ROLE), function (Builder $query) {
                                    $query->whereHas('priority.type.managers', function (Builder $query): void {
                                        $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                                    });
                                })
                                ->delete();

                            Notification::make()
                                ->title('Deleted ' . $deletedRecordsCount . ' service requests')
                                ->body(($deletedRecordsCount < $records->count()) ? ($records->count() - $deletedRecordsCount) . ' service requests were not deleted because you\'re not an auditor or manager of it.' : null)
                                ->success()
                                ->send();
                        })
                        ->fetchSelectedRecords(false),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Service Request'),
        ];
    }
}
