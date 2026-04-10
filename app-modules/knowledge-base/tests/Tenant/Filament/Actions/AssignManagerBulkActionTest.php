<?php

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ListKnowledgeBaseItems;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can bulk assign managers to items', function () {
    asSuperAdmin();

    $manager = User::factory()->create();

    $items = KnowledgeBaseItem::factory(5)->create();

    livewire(ListKnowledgeBaseItems::class)
        ->selectTableRecords($items->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$manager->id],
                'remove_prior' => false,
            ]
        )
        ->assertHasNoErrors();

    $items->each(function (KnowledgeBaseItem $item) use ($manager) {
        expect($item->refresh()->managers->pluck('id'))->toContain($manager->id);
    });
});

it('can bulk assign managers to items without removing previously assigned managers', function () {
    asSuperAdmin();

    $oldManager = User::factory()->create();
    $newManager = User::factory()->create();

    $items = KnowledgeBaseItem::factory(5)->create();

    $items->each(function (KnowledgeBaseItem $item) use ($oldManager) {
        $item->managers()->attach($oldManager->id);
    });

    livewire(ListKnowledgeBaseItems::class)
        ->selectTableRecords($items->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$newManager->id],
                'remove_prior' => false,
            ]
        )
        ->assertHasNoErrors();

    $items->each(function (KnowledgeBaseItem $item) use ($oldManager, $newManager) {
        expect($item->refresh()->managers->pluck('id'))->toContain($oldManager->id, $newManager->id);
    });
});

it('can bulk assign managers to items with removing previously assigned managers', function () {
    asSuperAdmin();

    $oldManager = User::factory()->create();
    $newManager = User::factory()->create();

    $items = KnowledgeBaseItem::factory(5)->create();

    $items->each(function (KnowledgeBaseItem $item) use ($oldManager) {
        $item->managers()->attach($oldManager->id);
    });

    livewire(ListKnowledgeBaseItems::class)
        ->selectTableRecords($items->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$newManager->id],
                'remove_prior' => true,
            ]
        )
        ->assertHasNoErrors();

    $items->each(function (KnowledgeBaseItem $item) use ($oldManager, $newManager) {
        expect($item->refresh()->managers->pluck('id'))->toContain($newManager->id);
        expect($item->managers->pluck('id'))->not()->toContain($oldManager->id);
    });
});
