<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages;

use Cknow\Money\Money;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use AidingApp\ServiceManagement\Models\ContractType;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->string()
                    ->required(),
                Select::make('contract_type_id')
                    ->required()
                    ->label('Contract Type')
                    ->relationship(
                        name: 'contractType',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order', 'ASC')
                    )
                    ->preload()
                    ->default(
                        fn () => ContractType::query()
                            ->where('is_default', true)
                            ->first()
                            ?->getKey()
                    )
                    ->searchable(),
                TextInput::make('vendor_name')
                    ->string()
                    ->label('Vendor Name')
                    ->required(),
                TextInput::make('contract_value')
                    ->label('Contract Value')
                    ->prefix('$')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->rule('decimal:0,2')
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->native(false)
                    ->displayFormat('m/d/Y')
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn (Get $get, Set $set) => $get('start_date') > $get('end_date') ? $set('end_date', '') : ''
                    )
                    ->closeOnDateSelection()
                    ->placeholder('Select start date')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->native(false)
                    ->displayFormat('m/d/Y')
                    ->placeholder('Select end date')
                    ->minDate(fn (Get $get) => $get('start_date'))
                    ->live(onBlur: true)
                    ->closeOnDateSelection()
                    ->required(),
                Textarea::make('description')
                    ->string()
                    ->nullable(),
                SpatieMediaLibraryFileUpload::make('contract_files')
                    ->label('Contract Files')
                    ->disk('s3')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.ms-word',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->downloadable()
                    ->maxFiles(5)
                    ->visibility('private')
                    ->multiple(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['contract_value'] = Money::parseByDecimal($data['contract_value'], config('money.defaultCurrency'));

        return $data;
    }
}
