<?php

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Filament\Widgets\KnowledgeBaseItemConcernsTable;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('returns all new concerns for a given knowledge base item by default', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $newConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $archivedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $resolvedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();
    
    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
        ->assertCanSeeTableRecords($newConcerns)
        ->assertCanNotSeeTableRecords($archivedConcerns->merge($resolvedConcerns));
});

it('can filter concerns by status', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $newConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($knowledgeBaseItem, 'knowledge$knowledgeBaseItem')
        ->create();

    $archivedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($knowledgeBaseItem, 'knowledge$knowledgeBaseItem')
        ->create();

    $resolvedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($knowledgeBaseItem, 'knowledge$knowledgeBaseItem')
        ->create();

    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
        ->filterTable('status', ConcernStatus::Archived->value)
        ->assertCanSeeTableRecords($archivedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($resolvedConcerns))
        ->filterTable('status', ConcernStatus::Resolved->value)
        ->assertCanSeeTableRecords($resolvedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($archivedConcerns));
});

it('can change the status of a concern properly', function () {
    asSuperAdmin();

    $concern = KnowledgeBaseItemConcern::factory()->create(['status' => ConcernStatus::New]);

    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $concern->knowledgeBaseItem])
        ->callTableAction('changeConcernStatus', $concern->getKey(), ['status' => ConcernStatus::Resolved])
        ->assertHasNoErrors();

    expect($concern->refresh()->status)->toBe(ConcernStatus::Resolved);
});
