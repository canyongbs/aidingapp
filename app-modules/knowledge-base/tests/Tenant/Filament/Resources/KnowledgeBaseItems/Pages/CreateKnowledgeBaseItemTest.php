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

use AidingApp\Division\Models\Division;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\CreateKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ListKnowledgeBaseItems;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Tests\Tenant\Filament\Resources\KnowledgeBaseItems\RequestFactories\CreateKnowledgeBaseItemRequestFactory;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

// TODO: Write CreateKnowledgeBaseItem tests
//test('A successful action on the CreateKnowledgeBaseItem page', function () {});
//
//test('CreateKnowledgeBaseItem requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    Division::factory(3)->create();

    actingAs($user);

    $user->givePermissionTo('knowledge_base_item.view-any');

    livewire(ListKnowledgeBaseItems::class)
        ->assertSuccessful()
        ->assertActionDisabled('create');

    $user->givePermissionTo('knowledge_base_item.create');

    livewire(ListKnowledgeBaseItems::class)
        ->assertActionEnabled('create');

    $request = collect(CreateKnowledgeBaseItemRequestFactory::new()->create());

    livewire(ListKnowledgeBaseItems::class)
        ->callAction('create', $request->toArray())
        ->assertHasNoActionErrors();

    assertCount(1, KnowledgeBaseItem::all());

    $data = $request->except('division')->toArray();

    assertDatabaseHas(KnowledgeBaseItem::class, $data);

    $knowledgeBaseItem = KnowledgeBaseItem::first();

    expect($knowledgeBaseItem->division->pluck('id')->toArray())->toEqual($request['division']);
});

test('CreateKnowledgeBaseItem is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    Division::factory(3)->create();

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    livewire(ListKnowledgeBaseItems::class)
        ->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    livewire(ListKnowledgeBaseItems::class)
        ->assertSuccessful();

    $request = collect(CreateKnowledgeBaseItemRequestFactory::new()->create());

    livewire(ListKnowledgeBaseItems::class)
        ->callAction('create', $request->toArray())
        ->assertHasNoActionErrors();

    assertCount(1, KnowledgeBaseItem::all());

    $data = $request->except('division')->toArray();

    assertDatabaseHas(KnowledgeBaseItem::class, $data);

    $knowledgeBaseItem = KnowledgeBaseItem::first();

    expect($knowledgeBaseItem->division->pluck('id')->toArray())->toEqual($request['division']);
});

// UserSelect (manager_ids field) admin-filtering tests
// No pre-selected scenario on Create — no persisted record exists yet.

test('manager_ids UserSelect does not show admin users in options by default on CreateKnowledgeBaseItem', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo('knowledge_base_item.view-any');
    $actor->givePermissionTo('knowledge_base_item.create');
    actingAs($actor);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(CreateKnowledgeBaseItem::class)
        ->assertSuccessful()
        ->assertFormFieldExists('manager_ids', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

test('manager_ids UserSelect shows all users when filter_admins_from_selection config is false on CreateKnowledgeBaseItem', function () {
    Config::set('app.filter_admins_from_selection', false);

    $actor = User::factory()->create();
    $actor->givePermissionTo('knowledge_base_item.view-any');
    $actor->givePermissionTo('knowledge_base_item.create');
    actingAs($actor);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(CreateKnowledgeBaseItem::class)
        ->assertSuccessful()
        ->assertFormFieldExists('manager_ids', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});
