<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\Actions;

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Features\KnowledgeBaseItemConcernFeature;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;

class CreateConcernAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->visible(KnowledgeBaseItemConcernFeature::active())
            ->label('Raise Concern')
            ->button()
            ->modalDescription('Please articulate the concern you have with this knowledge base item. You may enter up to 100 characters in the box below.')
            ->schema([
                Textarea::make('description')
                    ->hiddenLabel()
                    ->maxLength(100)
                    ->required(),
            ])
            ->action(function (array $data, KnowledgeBaseItem $record, Page $livewire): void {
                $record->concerns()->create([
                    'description' => $data['description'],
                    'status' => ConcernStatus::New
                ]);

                $livewire->dispatch('concern-created');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'raiseConcern';
    }
}