<?php

namespace AidingApp\Group\Filament\Resources\Groups\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Properties')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->string()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
