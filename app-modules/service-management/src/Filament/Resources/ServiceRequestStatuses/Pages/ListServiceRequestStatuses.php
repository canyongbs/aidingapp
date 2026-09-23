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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\Actions\UnarchiveServiceRequestStatusAction;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\ServiceRequestStatusResource;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Features\ServiceRequestStatusArchivingFeature;
use App\Filament\Tables\Columns\IdColumn;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListServiceRequestStatuses extends ListRecords
{
    protected static string $resource = ServiceRequestStatusResource::class;

    public function table(Table $table): Table
    {
        $isArchivingActive = ServiceRequestStatusArchivingFeature::active();

        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->formatStateUsing(fn (ServiceRequestStatus $record, string $state): string => $record->isArchived()
                        ? "{$state} (Archived)"
                        : $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classification')
                    ->label('Classification'),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (ServiceRequestStatus $serviceRequestStatus) => $serviceRequestStatus->color->value),
                TextColumn::make('service_requests_count')
                    ->label('# of Service Requests')
                    ->counts('serviceRequests')
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort')
            ->reorderable('sort', function (): bool {
                $trashedFilterValue = $this->getTableFilterState('trashed')['value'] ?? null;
                $archivedFilterValue = $this->getTableFilterState('archived')['value'] ?? null;

                return is_null($trashedFilterValue) && is_null($archivedFilterValue);
            })
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                UnarchiveServiceRequestStatusAction::make(),
            ])
            ->toolbarActions($isArchivingActive ? [
                BulkActionGroup::make([
                    ArchiveBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ] : [])
            ->filters([
                TrashedFilter::make(),
                ...($isArchivingActive ? [
                    TernaryFilter::make('archived')
                        ->label('Archived')
                        ->placeholder('Without archived records')
                        ->trueLabel('With archived records')
                        ->falseLabel('Only archived records')
                        ->queries(
                            true: fn (Builder $query): Builder => $query,
                            false: $this->onlyArchived(...),
                            blank: $this->withoutArchived(...),
                        )
                        // Without this the default `withoutArchived()` would hide the very record
                        // the unarchive action needs to resolve.
                        ->excludeWhenResolvingRecord(),
                ] : []),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * @param Builder<ServiceRequestStatus> $query
     *
     * @return Builder<ServiceRequestStatus>
     */
    protected function onlyArchived(Builder $query): Builder
    {
        return $query->onlyArchived();
    }

    /**
     * @param Builder<ServiceRequestStatus> $query
     *
     * @return Builder<ServiceRequestStatus>
     */
    protected function withoutArchived(Builder $query): Builder
    {
        return $query->withoutArchived();
    }
}
