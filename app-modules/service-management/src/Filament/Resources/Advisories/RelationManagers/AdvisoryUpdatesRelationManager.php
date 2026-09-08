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

namespace AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers;

use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates\AdvisoryUpdateResource;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Features\AdvisoryUpdateTitleAndDateFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdvisoryUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'advisoryUpdates';

    protected static ?string $relatedResource = AdvisoryUpdateResource::class;

    public function form(Schema $schema): Schema
    {
        assert($this->getOwnerRecord() instanceof Advisory);

        return $schema
            ->components([
                AdvisoryUpdateResource::getPropertiesSectionSchema(),
                ToggleButtons::make('status_id')
                    ->label('Status')
                    ->inline()
                    ->options(fn (): array => AdvisoryUpdateResource::getStatusOptions($this->getOwnerRecord()))
                    ->default($this->getOwnerRecord()->status->getKey())
                    ->exists((new AdvisoryStatus())->getTable(), 'id')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        assert($this->getOwnerRecord() instanceof Advisory);

        $advisory = $this->getOwnerRecord();

        return $table
            ->columns([
                IdColumn::make(),
                ...(AdvisoryUpdateTitleAndDateFeature::active()
                    ? [
                        TextColumn::make('title')
                            ->label('Title')
                            ->description(fn (AdvisoryUpdate $record): string => $record->update)
                            ->url(fn (AdvisoryUpdate $record): string => static::getRecordUrl($record, $advisory))
                            ->searchable(),
                    ]
                    : [
                        TextColumn::make('update')
                            ->label('Update')
                            ->words(6)
                            ->url(fn (AdvisoryUpdate $record): string => static::getRecordUrl($record, $advisory)),
                    ]),
                IconColumn::make('internal')
                    ->boolean(),
                ...(AdvisoryUpdateTitleAndDateFeature::active()
                    ? [
                        TextColumn::make('date')
                            ->label('Date')
                            ->dateTime()
                            ->sortable(),
                    ]
                    : [
                        TextColumn::make('created_at')
                            ->sortable(),
                        TextColumn::make('updated_at')
                            ->sortable(),
                    ]),
            ])
            ->defaultSort(AdvisoryUpdateTitleAndDateFeature::active() ? 'date' : 'created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->visible($this->getOwnerRecord()->status->classification === SystemAdvisoryStatusClassification::Resolved ? false : true)
                    ->after(function (array $data, AdvisoryUpdate $advisoryUpdate) {
                        $advisoryUpdate->advisory->update(['status_id' => $data['status_id']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }

    protected static function getRecordUrl(AdvisoryUpdate $record, Advisory $advisory): string
    {
        $routeParameters = ['record' => $record, 'advisory' => $advisory];

        return auth()->user()->can('update', $record)
            ? AdvisoryUpdateResource::getUrl('edit', $routeParameters)
            : AdvisoryUpdateResource::getUrl('view', $routeParameters);
    }
}
