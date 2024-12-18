<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality;
use AidingApp\KnowledgeBase\Tests\KnowledgeBaseQuality\RequestFactories\CreateKnowledgeBaseQualityRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

// TODO: Write CreateKnowledgeBaseQuality tests
//test('A successful action on the CreateKnowledgeBaseQuality page', function () {});
//
//test('CreateKnowledgeBaseQuality requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseQuality is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseQualityRequestFactory::new()->create());

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseQuality::all());

    assertDatabaseHas(KnowledgeBaseQuality::class, $request->toArray());
});

test('CreateKnowledgeBaseQuality is gated with proper feature ccess control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseQualityRequestFactory::new()->create());

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseQuality::all());

    assertDatabaseHas(KnowledgeBaseQuality::class, $request->toArray());
});
