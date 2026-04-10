<?php

namespace AidingApp\KnowledgeBase\Filament\Widgets;

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Filament\Resources\Actions\ChangeConcernStatusAction;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class KnowledgeBaseItemConcernsTable extends TableWidget
{
    public KnowledgeBaseItem $record;

    protected statis ?string $heading = 'Concerns Raised';

    public function mount(KnowledgeBaseItem $record): void
    {
        $this->record = $record;
    }

    #[On('concern-created')]
    public function conernCreated(): void {}

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => KnowledgeBaseItemConcern::whereBelongsTo($this->record, 'knowledgeBaseItem'))
            ->columns([
                TextColumn::make('createdBy.name')
                    ->label('Name'),
                TextColumn::make('description')
                    ->label('Concern'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),
                TextColumn::make('status'),
            ])
            ->recordActions([
                ChangeConcernStatusAction::make()
                    ->authorize('update', $this->record),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(ConcernStatus::class)
                    ->default([ConcernStatus::New->value]),
            ])
    }
}