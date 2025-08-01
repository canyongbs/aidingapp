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

use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\CreateOrganization;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Tests\Tenant\Organization\RequestFactories\CreateOrganizationRequestFactory;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('Create Organization is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateOrganization::class)
        ->assertForbidden();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('create')
        )->assertSuccessful();
});

test('validating that domain is required', function () {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    $request = collect(CreateOrganizationRequestFactory::new()->state([
        'domains' => [
            ['domain' => null],
        ]])->create());

    actingAs($user);

    livewire(CreateOrganization::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors([
            'domains.0.domain' => 'required',
        ]);

    $undoRepeaterFake();
});

test('validating that distinct domain is allowed', function () {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    $request = collect(CreateOrganizationRequestFactory::new()->state([
        'domains' => [
            ['domain' => 'google.com'],
            ['domain' => 'google.com'],
        ]])->create());

    actingAs($user);

    livewire(CreateOrganization::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors([
            'domains.0.domain',
        ]);

    $undoRepeaterFake();
});

test('if the domain is already in use then not be used second time.', function () {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    Organization::factory()
        ->state([
            'domains' => [
                ['domain' => 'google.com'],
                ['domain' => 'yahoo.com'],
            ],
        ])->create();

    $request = collect(CreateOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => 'yahoo.com'],
            ],
        ])->create());

    actingAs($user);

    livewire(CreateOrganization::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors([
            'domains.0.domain',
        ]);

    $undoRepeaterFake();
});

test('check if the provided domain is invalid', function (string $domain) {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    $request = collect(CreateOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => $domain],
            ]])
        ->create());

    actingAs($user);

    livewire(CreateOrganization::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors([
            'domains.0.domain' => 'regex',
        ]);

    $undoRepeaterFake();
})
    ->with([
        'test example.com',
        '.example.com',
        'sub*domain.example.com',
        'subdomain!.example.com',
        'sub..domain.example.com',
        'example!.com',
        'localhost',
    ]);
