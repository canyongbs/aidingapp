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

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategories\KnowledgeBaseCategoryResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategories\Pages\ListKnowledgeBaseCategories;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// TODO: Write ListKnowledgeBaseCategory tests
//test('The correct details are displayed on the ListKnowledgeBaseCategory page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertSuccessful();
});

test('ListKnowledgeBaseCategory is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertSuccessful();
});

it('only shows the bulk delete action to a user with the settings.delete permission', function () {
    KnowledgeBaseCategory::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('settings.view-any', 'settings.*.view');

    actingAs($user);

    livewire(ListKnowledgeBaseCategories::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('settings.*.delete');

    livewire(ListKnowledgeBaseCategories::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});
