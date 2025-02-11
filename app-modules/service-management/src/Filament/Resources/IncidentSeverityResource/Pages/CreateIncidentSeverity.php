<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages;

use AidingApp\ServiceManagement\Enums\IncidentSeverityColorOptions;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateIncidentSeverity extends CreateRecord
{
    protected static string $resource = IncidentSeverityResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Select::make('color')
                    ->label('Color')
                    ->required()
                    ->searchable()
                    ->options(IncidentSeverityColorOptions::class)
                    ->enum(IncidentSeverityColorOptions::class),
            ]);
    }
}
