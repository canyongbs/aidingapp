<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Product Name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('Product Link')
                    ->maxLength(255)
                    ->url(),
                Textarea::make('description')
                    ->label('Description')
                    ->string(),
                TextInput::make('version')
                    ->label('version')
                    ->maxLength(255),
                Textarea::make('additional_notes')
                    ->label('Additional Notes')
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
