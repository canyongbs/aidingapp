<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;

class EditOrganizationIndustry extends EditRecord
{
    protected static string $resource = OrganizationIndustryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->autofocus()
                            ->translateLabel()
                            ->maxLength(255)
                            ->required()
                            ->string()
                            ->placeholder('Organization Industry Name'),

                        Toggle::make('is_default')
                            ->label('Default')
                            ->live()
                            ->hint(function (?OrganizationIndustry $record, $state): ?string {
                                if ($record?->is_default) {
                                    return null;
                                }

                                if (! $state) {
                                    return null;
                                }

                                $currentDefault = OrganizationIndustry::query()
                                    ->where('is_default', true)
                                    ->value('name');

                                if (blank($currentDefault)) {
                                    return null;
                                }

                                return "The current default status is '{$currentDefault}', you are replacing it.";
                            })
                            ->hintColor('danger')
                            ->columnStart(1),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
