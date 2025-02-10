<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource;
use AidingApp\KnowledgeBase\Models\IncidentSeverity;
use AidingApp\KnowledgeBase\Models\IncidentStatus;
use AidingApp\Team\Models\Team;
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
                    ->required()
                    ->searchable()
                    ->relationship('severity', 'name')
                    // ->options(
                    //     IncidentSeverity::query()
                    //         ->get()
                    //         ->mapWithKeys(fn ($severity) => [$severity->id => $severity->name])
                    //         ->toArray(),
                    // )
                    ->exists(
                        table: (new IncidentSeverity())->getTable(),
                        column: (new IncidentSeverity())->getKeyName()
                    ),
                Select::make('status_id')
                    ->label('Status')
                    ->required()
                    ->searchable()
                    ->relationship('status', 'name')
                    ->exists(
                        table: (new IncidentStatus())->getTable(),
                        column: (new IncidentStatus())->getKeyName()
                    ),
                Select::make('assigned_team_id')
                    ->label('Assigned Team')
                    ->required()
                    ->searchable()
                    ->relationship('team', 'name')
                    // ->options(
                    //     Team::query()
                    //         ->get()
                    //         ->mapWithKeys(fn ($team) => [$team->id => $team->name])
                    //         ->toArray(),
                    // )
                    ->exists(
                        table: (new Team())->getTable(),
                        column: (new Team())->getKeyName()
                    ),
            ]);
    }
}
