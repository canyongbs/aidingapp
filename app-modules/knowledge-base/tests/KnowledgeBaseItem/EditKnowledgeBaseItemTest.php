<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

// TODO: Write EditKnowledgeBaseItem tests
//test('A successful action on the EditKnowledgeBaseItem page', function () {});
//
//test('EditKnowledgeBaseItem requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    get(
        KnowledgeBaseItemResource::getUrl('edit', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertForbidden();

    livewire(EditKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.update');

    get(
        KnowledgeBaseItemResource::getUrl('edit', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertSuccessful();

    // TODO Restore testing the edit form
});

test('EditKnowledgeBaseItem is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.update');

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    get(
        KnowledgeBaseItemResource::getUrl('edit', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertForbidden();

    livewire(EditKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    get(
        KnowledgeBaseItemResource::getUrl('edit', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertSuccessful();

    // TODO Restore testing the edit form
});
