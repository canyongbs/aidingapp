<?php

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ViewKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Filament\Widgets\KnowledgeBaseItemConcernsTable;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('is gated by proper access control', function () {
  $user = User::factory()->create();
  $user->givePermissionTo('knowledge_base_item.view-any');

  asSuperAdmin();

  $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();
  KnowledgeBaseItemConcern::factory()->for($knowledgeBaseItem)->create();

  actingAs($user);

  livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
    ->assertActionHidden([TestAction::make('changeConcernStatus')->table()]);

  $user->givePermissionTo('knowledge_base_item.*.update');

  livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
    ->assertCountTableRecords(1)
    ->assertActionVisible([TestAction::make('changeConcernStatus')->table()]);
});