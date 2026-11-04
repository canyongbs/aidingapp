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

use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\EditOrganization;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\OrganizationDomainsLock;
use AidingApp\Contact\Tests\Tenant\Organization\RequestFactories\EditOrganizationRequestFactory;
use App\Features\OrganizationArchivingFeature;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Tests\asSuperAdmin;

test('Edit Organization is gated with proper access control', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('edit', [
                'record' => $organization,
            ])
        )->assertForbidden();

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('edit', [
                'record' => $organization,
            ])
        )->assertSuccessful();
    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('Edit Organization Record', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    actingAs($user);

    $request = collect(EditOrganizationRequestFactory::new()->create());

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->set('data.domains', null)
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $organization->refresh();

    expect($organization->name)->toEqual($request->get('name'))
        ->and($organization->email)->toEqual($request->get('email'))
        ->and($organization->phone_number)->toEqual($request->get('phone_number'))
        ->and($organization->website)->toEqual($request->get('website'))
        ->and($organization->type_id)->toEqual($request->get('type_id'))
        ->and($organization->industry_id)->toEqual($request->get('industry_id'))
        ->and($organization->number_of_employees)->toEqual($request->get('number_of_employees'))
        ->and($organization->address)->toEqual($request->get('address'))
        ->and($organization->city)->toEqual($request->get('city'))
        ->and($organization->state)->toEqual($request->get('state'))
        ->and($organization->postalcode)->toEqual($request->get('postalcode'))
        ->and($organization->country)->toEqual($request->get('country'))
        ->and($organization->linkedin_url)->toEqual($request->get('linkedin_url'))
        ->and($organization->facebook_url)->toEqual($request->get('facebook_url'))
        ->and($organization->twitter_url)->toEqual($request->get('twitter_url'))
        ->and($organization->domains)->toEqual($request->get('domains'));
});

test('validating that domain is required', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $request = collect(EditOrganizationRequestFactory::new()->state([
        'domains' => [
            ['domain' => null],
        ]])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors(['domains.0.domain' => 'required']);

    $undoRepeaterFake();
});

test('validating that distinct domain is allowed', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => 'google.com'],
                ['domain' => 'google.com'],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors([
            'domains.0.domain',
        ]);

    $undoRepeaterFake();
});

test('if the domain is already in use then not be used second time.', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $existingOrganization = Organization::factory()
        ->state([
            'domains' => [
                ['domain' => 'hotmail.com'],
                ['domain' => 'gmail.com'],
            ],
        ])->create();

    $organization = Organization::factory()->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => 'hotmail.com'],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors([
            'domains.0.domain',
        ]);

    $undoRepeaterFake();
});

test('a domain that only differs by case or www from one stored by another organization cannot be used', function (string $domain): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    Organization::factory()
        ->state([
            'domains' => [
                ['domain' => 'Shared.edu'],
            ],
        ])->create();

    $organization = Organization::factory()->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => $domain],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors([
            'domains.0.domain',
        ]);

    $undoRepeaterFake();
})->with([
    'lowercase' => 'shared.edu',
    'uppercase' => 'SHARED.EDU',
    'www prefix' => 'www.shared.edu',
]);

test('an organization can keep its own domain when it is resubmitted in a different case', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()
        ->state([
            'domains' => [
                ['domain' => 'Shared.edu'],
            ],
        ])->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'name' => $organization->name,
            'domains' => [
                ['domain' => 'shared.edu'],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($organization->refresh()->domains[0])->toBe(['domain' => 'shared.edu']);

    $undoRepeaterFake();
});

test('check if the provided domain is invalid', function (string $domain): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => $domain],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
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
        'https://www.example.com',
        'example.com::8080',
        'mydomain.com/path',
        '-mydomain.com',
        'mydomain-.com',
        'sub.-domain.com',
        'sub-.domain.com',
    ]);

test('check if the provided domain is valid', function (string $domain): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create();

    $request = collect(EditOrganizationRequestFactory::new()
        ->state([
            'domains' => [
                ['domain' => $domain],
            ],
        ])->create());

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();
})
    ->with([
        'www.example.com',
        'google.com',
        'example.co.uk',
        'test-domain-123.com',
        'subdomain.example.org',
    ]);

test('the organization domains lock is taken when domains are saved', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create();

    actingAs($user);

    mock(OrganizationDomainsLock::class)->shouldReceive('acquire')->once();

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm(['domains' => [['domain' => 'updated.edu']]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($organization->refresh()->domains)->toBe([['domain' => 'updated.edu']]);

    $undoRepeaterFake();
});

test('the organization domains lock is not taken when no domains are saved', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create();

    actingAs($user);

    mock(OrganizationDomainsLock::class)->shouldNotReceive('acquire');

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm(['domains' => []])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();
});

test('the organization name must be unique among other non-trashed organizations', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    Organization::factory()->create(['name' => 'Acme Corporation']);
    $organization = Organization::factory()->create(['name' => 'Globex']);

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm(['name' => 'Acme Corporation'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    $undoRepeaterFake();
});

test('an organization can keep its own name when editing', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create(['name' => 'Acme Corporation']);

    actingAs($user);

    Livewire::test(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->set('data.domains', null)
        ->fillForm(['name' => 'Acme Corporation'])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();
});

it('returns not found for an archived organization', function (): void {
    OrganizationArchivingFeature::activate();

    actingAs(User::factory()->create()->givePermissionTo('organization.view-any', 'organization.*.update'));

    $organization = Organization::factory()->archived()->create();

    get(OrganizationResource::getUrl('edit', ['record' => $organization]))
        ->assertNotFound();
});

describe('archiving', function (): void {
    it('archives the organization', function (): void {
        OrganizationArchivingFeature::activate();

        asSuperAdmin();

        $organization = Organization::factory()->create();

        expect($organization->isArchived())->toBeFalse();

        Livewire::test(EditOrganization::class, ['record' => $organization->getRouteKey()])
            ->callAction('archive')
            ->assertRedirect(OrganizationResource::getUrl('index'));

        expect($organization->refresh()->isArchived())->toBeTrue();
    });
});

it('shows the delete action when the organization archiving feature is inactive', function (): void {
    OrganizationArchivingFeature::deactivate();

    asSuperAdmin();

    $organization = Organization::factory()->create();

    Livewire::test(EditOrganization::class, ['record' => $organization->getRouteKey()])
        ->assertActionVisible('delete')
        ->assertActionDoesNotExist('archive');
});

it('explains that an archived organization reserves the name when editing', function (): void {
    OrganizationArchivingFeature::activate();

    $user = User::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    Organization::factory()->archived()->create(['name' => 'Archived Org']);
    $organization = Organization::factory()->create();

    actingAs($user);

    $component = Livewire::test(EditOrganization::class, ['record' => $organization->getRouteKey()])
        ->set('data.domains', null)
        ->fillForm(['name' => 'archived org'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'An archived organization with this name already exists.']);

    expect($component->errors()->get('data.name'))->toBe(['An archived organization with this name already exists.']);
});

it('allows saving an organization with its own unchanged name', function (): void {
    OrganizationArchivingFeature::activate();

    $user = User::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    $organization = Organization::factory()->create(['name' => 'Own Org']);

    actingAs($user);

    Livewire::test(EditOrganization::class, ['record' => $organization->getRouteKey()])
        ->set('data.domains', null)
        ->fillForm(['name' => 'Own Org'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('uses the unique message for an archived organization name when editing while organization archiving is inactive', function (): void {
    OrganizationArchivingFeature::deactivate();

    $user = User::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    Organization::factory()->archived()->create(['name' => 'Archived Org']);
    $organization = Organization::factory()->create();

    actingAs($user);

    $component = Livewire::test(EditOrganization::class, ['record' => $organization->getRouteKey()])
        ->set('data.domains', null)
        ->fillForm(['name' => 'Archived Org'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($component->errors()->get('data.name'))->not->toContain('An archived organization with this name already exists.');
});
