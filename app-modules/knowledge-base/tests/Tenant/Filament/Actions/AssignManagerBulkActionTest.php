<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
