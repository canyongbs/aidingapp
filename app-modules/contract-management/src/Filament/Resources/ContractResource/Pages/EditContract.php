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

namespace AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages;

use AidingApp\ContractManagement\Filament\Resources\ContractResource;
use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Models\ContractType;
use Cknow\Money\Money;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EditContract extends EditRecord
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
                        name : 'contractType',
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
