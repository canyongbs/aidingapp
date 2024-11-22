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

namespace AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use App\Filament\Tables\Columns\MaskColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Infolists\Components\MaskTextEntry;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource;

class ManageProductLicenses extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'product_licenses';

    protected static ?string $navigationLabel = 'Licenses';

    protected static ?string $breadcrumb = 'License';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license')
                    ->label('License')
                    ->string()
                    ->required(),
                Select::make('assigned_to')
                    ->relationship(
                        name: 'contact',
                        titleAttribute: 'full_name',
                    )
                    ->label('Assigned To')
                    ->native(false)
                    ->preload()
                    ->searchable(),
                Textarea::make('description')
                    ->label('Description')
                    ->string()
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->displayFormat('d-m-Y')
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
                        ->displayFormat('d-m-Y')
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
                MaskColumn::make('license')
                    ->label('License')
                    ->sortable(),
                TextColumn::make('contact.full_name')
                    ->label('Assigned To'),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->sortable()
                    ->dateTime('d-m-Y'),
                TextColumn::make('expiration_date')
                    ->label('Expiration Date')
                    ->sortable()
                    ->dateTime('d-m-Y'),
                TextColumn::make('Status')
                    ->label('Status'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        MaskTextEntry::make('license')
                            ->label('License'),
                        TextEntry::make('contact.full_name')
                            ->label('Assigned To'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date('d-m-Y'),
                        TextEntry::make('expiration_date')
                            ->label('Expiration Date')
                            ->date('d-m-Y')
                            ->placeholder('License Does Not Expire'),
                    ]),
            ]);
    }
}
