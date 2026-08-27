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

use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\CreateOrganizationIndustry;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Tests\Tenant\OrganizationIndustry\RequestFactories\CreateOrganizationIndustryRequestFactory;
use App\Features\OrganizationTypeAndIndustryNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('Create Organization Industry is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateOrganizationIndustry::class)
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('create')
        )->assertSuccessful();
});
test('Create New Organization Industry', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user);

    $request = collect(CreateOrganizationIndustryRequestFactory::new()->create());

    livewire(CreateOrganizationIndustry::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    assertCount(1, OrganizationIndustry::all());
    assertDatabaseHas(OrganizationIndustry::class, $request->toArray());
});

test('the organization industry name must be unique among non-trashed industries', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    OrganizationIndustry::factory()->create(['name' => 'Technology']);

    $request = collect(CreateOrganizationIndustryRequestFactory::new()->state(['name' => 'Technology'])->create());

    actingAs($user);

    livewire(CreateOrganizationIndustry::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('the organization industry name uniqueness check is case-insensitive', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    OrganizationIndustry::factory()->create(['name' => 'Technology']);

    $request = collect(CreateOrganizationIndustryRequestFactory::new()->state(['name' => 'TECHNOLOGY'])->create());

    actingAs($user);

    livewire(CreateOrganizationIndustry::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('does not apply the unique form rule when the feature is disabled', function () {
    OrganizationTypeAndIndustryNameUniquenessFeature::deactivate();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    OrganizationIndustry::factory()->create(['name' => 'Technology']);

    $request = collect(CreateOrganizationIndustryRequestFactory::new()->state(['name' => 'Technology'])->create());

    actingAs($user);

    expect(fn () => livewire(CreateOrganizationIndustry::class)
        ->fillForm($request->toArray())
        ->call('create'))
        ->toThrow(UniqueConstraintViolationException::class);
});
