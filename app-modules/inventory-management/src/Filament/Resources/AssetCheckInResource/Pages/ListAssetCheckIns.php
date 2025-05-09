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

namespace AidingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages;

use AidingApp\InventoryManagement\Enums\AssetCheckOutStatus;
use AidingApp\InventoryManagement\Filament\Resources\AssetCheckInResource;
use AidingApp\InventoryManagement\Filament\Resources\AssetResource;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListAssetCheckIns extends ListRecords
{
    protected static string $resource = AssetCheckInResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('asset.name')
                    ->url(fn ($record) => AssetResource::getUrl('view', ['record' => $record->asset]))
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('asset.type.name')
                    ->searchable(),
                TextColumn::make('checkOut.checked_out_at')
                    ->label('Checked out at')
                    ->dateTime('g:ia - M j, Y'),
                TextColumn::make('checked_in_at')
                    ->dateTime('g:ia - M j, Y'),
                TextColumn::make('created_at')
                    ->label('Status')
                    ->state(fn (AssetCheckIn $record): AssetCheckOutStatus => $record->checkOut->status)
                    ->formatStateUsing(fn (AssetCheckOutStatus $state) => $state->getLabel())
                    ->badge()
                    ->color(fn (AssetCheckOutStatus $state): string => match ($state) {
                        AssetCheckOutStatus::Returned => 'success',
                        AssetCheckOutStatus::Active => 'info',
                        default => 'danger',
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('View Returned Asset'),
            ]);
    }
}
