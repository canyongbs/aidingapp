<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
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
use App\Filament\Tables\Columns\MaskColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
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
                Textarea::make('description')
                    ->label('Description')
                    ->string(),
                Select::make('assigned_to')
                    ->relationship(
                        name: 'contact',
                        titleAttribute: 'full_name',
                    )
                    ->label('Assigned To')
                    ->native(false)
                    ->preload()
                    ->searchable(),
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
                Checkbox::make('license_does_not_expire')
                    ->label('License Does Not Expire')
                    ->live(),
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
}
