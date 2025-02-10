<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditIncidentSeverity extends EditRecord
{
    protected static string $resource = IncidentSeverityResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
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
