<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages;

use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;
use AidingApp\Contact\Models\OrganizationType;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Laravel\Pennant\Feature;

class EditOrganizationType extends EditRecord
{
    protected static string $resource = OrganizationTypeResource::class;

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
                        ->placeholder('Organization Type Name'),

                    Toggle::make('is_default')
                        ->label('Default')
                        ->live()
                        ->hint(function (?OrganizationType $record, $state): ?string {
                            if ($record?->is_default) {
                                return null;
                            }

                            if (! $state) {
                                return null;
                            }

                            $currentDefault = OrganizationType::query()
                                ->where('is_default', true)
                                ->value('name');

                            if (blank($currentDefault)) {
                                return null;
                            }

                            return "The current default status is '{$currentDefault}', you are replacing it.";
                        })
                        ->hintColor('danger')
                        ->columnStart(1)
                        
                ]),
        ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
        ];
    }
}
