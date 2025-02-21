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

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\CreateIncidentUpdate;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\EditIncidentUpdate;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\ListIncidentUpdates;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\ViewIncidentUpdate;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncidentUpdateResource extends Resource
{
    protected static ?string $model = IncidentUpdate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /* Select::make('incident_id')
                    ->relationship('incident', 'id')
                    ->preload()
                    ->label('Incident')
                    ->required()
                    ->exists(
                        table: (new Incident())->getTable(),
                        column: (new Incident())->getKeyName()
                    ),
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']), */
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /* IdColumn::make(),
                TextColumn::make('serviceRequest.respondent.full')
                    ->label('Related To')
                    ->sortable(query: function (Builder $query, string $direction, $record): Builder {
                        // TODO: Update this to work with other respondent types
                        return $query->join('service_requests', 'service_request_updates.service_request_id', '=', 'service_requests.id')
                            ->join('contacts', function ($join) {
                                $join->on('service_requests.respondent_id', '=', 'contacts.id')
                                    ->where('service_requests.respondent_type', '=', 'contact');
                            })
                            ->orderBy('full', $direction);
                    })
                    ->searchable(),
                TextColumn::make('serviceRequest.service_request_number')
                    ->label('Service Request')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('internal')
                    ->boolean()
                    ->label('Internal'), */
            ])
            /* ->filters([
                TernaryFilter::make('internal')
                    ->label('Internal'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]) */;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidentUpdates::route('/'),
            'create' => CreateIncidentUpdate::route('/create'),
            'view' => ViewIncidentUpdate::route('/{incident}'),
            'edit' => EditIncidentUpdate::route('/{incident}/edit'),
        ];
    }
}
