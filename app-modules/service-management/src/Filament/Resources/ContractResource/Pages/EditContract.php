<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages;

use Cknow\Money\Money;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use App\Features\ContractManagement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\ServiceManagement\Models\Contract;
use AidingApp\ServiceManagement\Models\ContractType;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected ?bool $hasDatabaseTransactions = true;

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
                        name : 'contractType', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order', 'ASC'))
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
                    ->disk('s3')
                    ->label('Contract Files')
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

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Contract $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['contract_value'] = Money::parseByDecimal($data['contract_value'], config('money.defaultCurrency'));

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['contract_value'] = $this->getRecord()->contract_value?->formatByDecimal();

        return $data;
    }
}
