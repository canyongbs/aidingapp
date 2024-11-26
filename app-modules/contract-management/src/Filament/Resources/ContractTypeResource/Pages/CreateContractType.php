<?php

namespace AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages;

use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AidingApp\ContractManagement\Models\ContractType;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource;

class CreateContractType extends CreateRecord
{
    protected static string $resource = ContractTypeResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->string()
                    ->required(),
                Toggle::make('is_default')
                    ->label('Default')
                    ->live()
                    ->hint(function (?ContractType $record, $state): ?string {
                        if ($record?->is_default) {
                            return null;
                        }

                        if (! $state) {
                            return null;
                        }

                        $currentDefault = ContractType::query()
                            ->where('is_default', true)
                            ->value('name');

                        if (blank($currentDefault)) {
                            return null;
                        }

                        return "The current default status is '{$currentDefault}', you are replacing it.";
                    })
                    ->hintColor('danger')
                    ->columnStart(1),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['order'] = DB::raw('COALESCE((SELECT MAX("order") FROM contract_types), 0) + 1');

        if ($data['is_default']) {
            ContractType::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
