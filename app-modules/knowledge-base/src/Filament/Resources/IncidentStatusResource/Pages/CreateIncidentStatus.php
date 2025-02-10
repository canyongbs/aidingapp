<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages;

use AidingApp\KnowledgeBase\Enums\SystemIncidentStatusClassification;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource;
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
                    ->searchable()
                    ->options(SystemIncidentStatusClassification::class)
                    ->required()
                    ->enum(SystemIncidentStatusClassification::class),
            ]);
    }
}
