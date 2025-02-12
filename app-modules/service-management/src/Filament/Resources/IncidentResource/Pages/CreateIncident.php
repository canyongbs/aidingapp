<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(65535)
                    ->string(),
                Select::make('severity_id')
                    ->label('Severity')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->relationship('severity', 'name'),
                Select::make('status_id')
                    ->label('Status')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('status', 'name'),
                Select::make('assigned_team_id')
                    ->label('Assigned Team')
                    ->preload()
                    ->searchable()
                    ->relationship('assignedTeam', 'name')
                    ->default(auth()->user()?->teams()->first()?->getKey()),
            ]);
    }
}
