<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use Filament\Forms\Components\Section;
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
                    ->string()
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->string()
                    ->maxLength(65535),
                TextInput::make('domain')
                    ->label('URL')
                    ->required()
                    ->maxLength(255)
                    ->url(),
                Select::make('frequency')
                    ->label('Frequency')
                    ->searchable()
                    ->options(ServiceMonitoringFrequency::class)
                    ->enum(ServiceMonitoringFrequency::class)
                    ->required(),
                Section::make('Notification Group')
                    ->schema([
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
                    ])
                    ->columns(2),
            ]);
    }
}
