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

namespace AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates;

use AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates\Pages\CreateAdvisoryUpdate;
use AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates\Pages\EditAdvisoryUpdate;
use AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates\Pages\ListAdvisoryUpdates;
use AidingApp\ServiceManagement\Filament\Resources\AdvisoryUpdates\Pages\ViewAdvisoryUpdate;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Enums\Feature;
use App\Features\IncidentRenameFeature;
use App\Filament\Tables\Columns\IdColumn;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class AdvisoryUpdateResource extends Resource
{
    protected static ?string $model = AdvisoryUpdate::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static bool $shouldRegisterNavigation = false;

    //TODO: IncidentRenameFeature clean up - remove entire canAccess when you remove the feature flag.
    public static function canAccess(): bool
    {
        return IncidentRenameFeature::active() && Gate::check(Feature::AdvisoryManagement->getGateName()) && auth()->user()->can('settings.view-any');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('advisory_id')
                    ->relationship('advisory', 'id')
                    ->preload()
                    ->label('Advisory')
                    ->required()
                    ->exists(
                        table: (new Advisory())->getTable(),
                        column: (new Advisory())->getKeyName()
                    ),
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('advisory.title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('internal')
                    ->boolean()
                    ->label('Internal'),
            ])
            ->filters([
                TernaryFilter::make('internal')
                    ->label('Internal'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdvisoryUpdates::route('/'),
            'create' => CreateAdvisoryUpdate::route('/create'),
            'view' => ViewAdvisoryUpdate::route('/{record}'),
            'edit' => EditAdvisoryUpdate::route('/{record}/edit'),
        ];
    }
}
