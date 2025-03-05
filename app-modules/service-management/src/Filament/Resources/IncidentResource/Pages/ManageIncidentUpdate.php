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

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ManageIncidentUpdate extends ManageRelatedRecords
{
    protected static string $resource = IncidentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'incidentUpdates';

    protected static ?string $navigationLabel = 'Updates';

    protected static ?string $breadcrumb = 'Updates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean'])
                    ->columnSpan('full'),
                Select::make('status_id')
                    ->label('Status')
                    ->options(fn () => IncidentStatus::orderBy('classification')
                        ->orderBy('name')
                        ->get(['id', 'name', 'classification'])
                        ->groupBy(fn (IncidentStatus $status) => $status->classification->getlabel())
                        ->map(fn (Collection $group) => $group->pluck('name', 'id')->map(
                            fn ($name, $id) => $name . ($id === $this->getOwnerRecord()->status->getKey() ? ' (Current)' : '')
                        )))
                    ->exists((new IncidentStatus())->getTable(), 'id')
                    ->default($this->getOwnerRecord()->status->getKey()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('update')
                    ->label('Update')
                    ->words(6),
                IconColumn::make('internal')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->visible($this->getOwnerRecord()?->status?->classification === SystemIncidentStatusClassification::Resolved ? false : true)
                    ->after(function ($data, IncidentUpdate $incidentUpdate) {
                        $incidentUpdate->incident->update(['status_id' => $data['status_id']]);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (IncidentUpdate $incidentUpdate) => IncidentUpdateResource::getUrl('view', ['record' => $incidentUpdate])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
