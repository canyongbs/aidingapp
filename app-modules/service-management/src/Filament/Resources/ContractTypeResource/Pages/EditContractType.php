<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource\Pages;

use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AidingApp\ServiceManagement\Models\ContractType;
use AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource;

class EditContractType extends EditRecord
{
    protected static string $resource = ContractTypeResource::class;

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

    /**
    * @return array<int|string, string|null>
    */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var ContractType $record */
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

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        if ($data['is_default']) {
            ContractType::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
