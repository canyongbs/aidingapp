<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateIncidentStatus extends CreateRecord
{
    protected static string $resource = IncidentStatusResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                Select::make('classification')
                    ->label('Classification')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->options(SystemIncidentStatusClassification::class)
                    ->enum(SystemIncidentStatusClassification::class),
            ]);
    }
}
