<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function getTitle(): string|Htmlable
    {
        assert($this->record instanceof KnowledgeBaseItem);

        return $this->record->title;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('knowledge-base::filament.pages.badges'),
                Tabs::make()
                    ->tabs([
                        Tab::make('Content')
                            ->label('Resource')
                            ->schema([
                                TextEntry::make('article_details')
                                    ->columnSpanFull()
                                    ->hiddenLabel()
                                    ->html(),
                            ])
                            ->id('content'),
                        Tab::make('Properties')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Article Title')
                                    ->columnSpanFull(),
                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                                TextEntry::make('public')
                                    ->label('Public')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                TextEntry::make('is_featured')
                                    ->label('Featured')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                TextEntry::make('tags')
                                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->tags->pluck('name'))
                                    ->badge(),
                            ])
                            ->id('properties')
                            ->columns(2),
                        Tab::make('Metadata')
                            ->schema([
                                TextEntry::make('quality.name')
                                    ->label('Quality'),
                                TextEntry::make('status.name')
                                    ->label('Status'),
                                TextEntry::make('category.name')
                                    ->label('Category'),
                                TextEntry::make('division.name')
                                    ->label('Division'),
                            ])
                            ->id('metadata'),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
