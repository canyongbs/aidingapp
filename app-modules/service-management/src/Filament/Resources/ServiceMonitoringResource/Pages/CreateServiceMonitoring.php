<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceMonitoring extends CreateRecord
{
    protected static string $resource = ServiceMonitoringResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->string()
                    ->maxLength(65535),
                TextInput::make('domain')
                    ->label('Domain')
                    ->required()
                    ->maxLength(255)
                    ->regex('/^(?!-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,63}$/'),
                Select::make('frequency')
                    ->label('Frequency')
                    ->searchable()
                    ->options(ServiceMonitoringFrequency::class)
                    ->required(),
                Select::make('team')
                    ->relationship('teams', 'name')
                    ->label('Team')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('user')
                    ->relationship('users', 'name')
                    ->label('User')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
