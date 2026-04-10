<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\Actions;

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class ChangeConcernStatusAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Change Status')
            ->button()
            ->outlined()
            ->modalDescription('Select what status this concern should have.')
            ->schema([
                Select::make('status')
                ->options(ConcernStatus::class)
                ->enum(ConcernStatus::class)
                ->default(fn(KnowledgeBaseItemConcern $record) => $record->status->value),
            ])
            ->action(function (array $data, KnowledgeBaseItemConcern $record): void {
                $record->status = $data['status'];

                $record->save();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'changeConcernStatus';
    }
}