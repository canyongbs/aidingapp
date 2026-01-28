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

namespace AidingApp\LicenseManagement\Filament\Resources\ProductResource\Pages;

use AidingApp\LicenseManagement\Filament\Resources\ProductResource;
use App\Filament\Infolists\Components\MaskedTextEntry;
use App\Filament\Tables\Columns\MaskedTextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageProductLicenses extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'productLicenses';

    protected static ?string $navigationLabel = 'Licenses';

    protected static ?string $breadcrumb = 'License';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('license')
                    ->label('License')
                    ->string()
                    ->required()
                    ->password(),
                Select::make('assigned_to')
                    ->relationship(
                        name: 'contact',
                        titleAttribute: 'full_name',
                    )
                    ->label('Assigned To')
                    ->preload()
                    ->searchable(),
                Textarea::make('description')
                    ->label('Description')
                    ->string()
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->displayFormat('m-d-Y')
                    ->closeOnDateSelection()
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(
                        fn (Get $get, Set $set) => $get('start_date') > $get('expiration_date') ? $set('expiration_date', '') : ''
                    ),
                Group::make([
                    Checkbox::make('license_does_not_expire')
                        ->label('License Does Not Expire')
                        ->live()
                        ->afterStateHydrated(function (Set $set, $state, $record) {
                            if ($record && is_null($record->expiration_date)) {
                                $set('license_does_not_expire', true);
                            }
                        }),
                    DatePicker::make('expiration_date')
                        ->label('Expiration Date')
                        ->default('No Expiration')
                        ->displayFormat('m-d-Y')
                        ->closeOnDateSelection()
                        ->required(fn (Get $get) => ! $get('license_does_not_expire'))
                        ->disabled(fn (Get $get) => $get('license_does_not_expire'))
                        ->minDate(fn (Get $get) => $get('start_date'))
                        ->after('start_date')
                        ->live(onBlur: true)
                        ->native(false),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                MaskedTextColumn::make('license')
                    ->label('License')
                    ->sortable(),
                TextColumn::make('contact.full_name')
                    ->label('Assigned To'),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->sortable()
                    ->dateTime('m-d-Y'),
                TextColumn::make('expiration_date')
                    ->label('Expiration Date')
                    ->placeholder('No Expiration')
                    ->sortable()
                    ->dateTime('m-d-Y'),
                TextColumn::make('Status')
                    ->label('Status'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        MaskedTextEntry::make('license')
                            ->label('License'),
                        TextEntry::make('contact.full_name')
                            ->label('Assigned To'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date('m-d-Y'),
                        TextEntry::make('expiration_date')
                            ->label('Expiration Date')
                            ->date('m-d-Y')
                            ->placeholder('License Does Not Expire'),
                    ]),
            ]);
    }
}
