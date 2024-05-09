<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Filament\Resources\TagResource;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('type')
                            ->options([
                                KnowledgeBaseItem::getTagType() => KnowledgeBaseItem::getTagLabel(),
                            ])
                            ->required()
                            ->string()
                            ->columnSpanFull()
                            ->live(),
                        TextInput::make('name')
                            ->autocomplete(false)
                            ->required()
                            ->string()
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('type', $get('type'));
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
