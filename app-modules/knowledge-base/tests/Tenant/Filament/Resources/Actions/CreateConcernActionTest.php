<?php

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ViewKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Tests\Tenant\Filament\Resources\Actions\RequestFactories\CreateConcernActionRequestFactory;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can create a concern properly', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $data = CreateConcernActionRequestFactory::new()->create();

    livewire(ViewKnowledgeBaseItem::class, ['record' => $knowledgeBaseItem->getKey()])
        ->callAction('raiseConcern', [
            'description' => $data['description'],
        ]);

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->conerns()->count())->toBe(1);
    expect($knowledgeBaseItem->conerns()->first()->description)->toBe($data['description']);
});
