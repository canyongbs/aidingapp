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

use AidingApp\Contact\Http\Requests\Api\V1\Organizations\UpdateOrganizationRequest;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use App\Models\SystemUser;

use function Pest\Laravel\patchJson;
use function Tests\actingAsApiUser;

$actingAsOrganizationUpdater = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');
    actingAsApiUser($user);

    return $user;
};

it('is gated with proper access control', function (): void {
    $organization = Organization::factory()->create(['name' => 'Original']);

    $user = SystemUser::factory()->create();
    actingAsApiUser($user);
    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Changed'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.*.update');
    actingAsApiUser($user);
    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Changed'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    actingAsApiUser($user);
    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Changed'])
        ->assertForbidden();

    expect($organization->refresh()->name)->toBe('Original');
});

it('checks permissions before validating the request', function (): void {
    $organization = Organization::factory()->create();

    $user = SystemUser::factory()->create();
    actingAsApiUser($user);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => ''])
        ->assertForbidden();
});

it('can update every attribute', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();
    $industry = OrganizationIndustry::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), [
        'name' => 'Updated Name',
        'email' => 'updated@acme.test',
        'phone_number' => '+15555550101',
        'website' => 'https://updated.test',
        'industry' => $industry->name,
        'description' => 'Updated description',
        'number_of_employees' => 10,
        'address' => '2 Road Runner Way',
        'city' => 'Tucson',
        'state' => 'AZ',
        'postalcode' => '85701',
        'country' => 'USA',
        'linkedin_url' => 'https://linkedin.com/company/updated',
        'facebook_url' => 'https://facebook.com/updated',
        'twitter_url' => 'https://twitter.com/updated',
        'domains' => ['updated.test'],
        'is_contact_generation_enabled' => true,
    ])
        ->assertOk()
        ->assertJsonPath('data.id', $organization->getKey())
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.industry.id', $industry->getKey())
        ->assertJsonPath('data.domains', ['updated.test'])
        ->assertJsonPath('data.is_contact_generation_enabled', true);

    expect($organization->refresh()->only([
        'name', 'email', 'phone_number', 'website', 'industry_id', 'description', 'number_of_employees',
        'address', 'city', 'state', 'postalcode', 'country', 'linkedin_url', 'facebook_url', 'twitter_url',
        'domains', 'is_contact_generation_enabled',
    ]))->toBe([
        'name' => 'Updated Name',
        'email' => 'updated@acme.test',
        'phone_number' => '+15555550101',
        'website' => 'https://updated.test',
        'industry_id' => $industry->getKey(),
        'description' => 'Updated description',
        'number_of_employees' => 10,
        'address' => '2 Road Runner Way',
        'city' => 'Tucson',
        'state' => 'AZ',
        'postalcode' => '85701',
        'country' => 'USA',
        'linkedin_url' => 'https://linkedin.com/company/updated',
        'facebook_url' => 'https://facebook.com/updated',
        'twitter_url' => 'https://twitter.com/updated',
        'domains' => [['domain' => 'updated.test']],
        'is_contact_generation_enabled' => true,
    ]);
});

it('only updates the attributes that are provided', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [['domain' => 'acme.test']],
    ]);
    $original = $organization->only(['email', 'industry_id', 'type_id', 'city', 'domains', 'is_contact_generation_enabled']);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Only Name'])
        ->assertOk();

    $organization->refresh();

    expect($organization->name)->toBe('Only Name');
    expect($organization->only(['email', 'industry_id', 'type_id', 'city', 'domains', 'is_contact_generation_enabled']))
        ->toBe($original);
});

it('can clear nullable attributes by providing null', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.test']]]);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), [
        'email' => null,
        'industry' => null,
        'type' => null,
        'number_of_employees' => null,
        'domains' => null,
    ])
        ->assertOk()
        ->assertJsonPath('data.industry', null)
        ->assertJsonPath('data.type', null)
        ->assertJsonPath('data.domains', []);

    expect($organization->refresh()->only(['email', 'industry_id', 'type_id', 'number_of_employees', 'domains']))
        ->toBe([
            'email' => null,
            'industry_id' => null,
            'type_id' => null,
            'number_of_employees' => null,
            'domains' => null,
        ]);
});

it('can clear the domains by providing an empty array', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.test']]]);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['domains' => []])
        ->assertOk()
        ->assertJsonPath('data.domains', []);
});

it('allows an organization to keep its own name and domains', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create(['name' => 'Acme', 'domains' => [['domain' => 'acme.test']]]);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), [
        'name' => 'Acme',
        'domains' => ['acme.test', 'acme.org'],
    ])
        ->assertOk()
        ->assertJsonPath('data.domains', ['acme.test', 'acme.org']);
});

it('validates the request', function (array $data, array $errors) use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);
})->with([
    'name cannot be null' => [['name' => null], ['name']],
    'name must be a string' => [['name' => 123], ['name']],
    'name is too long' => [['name' => str_repeat('a', 256)], ['name']],
    'email must be an email' => [['email' => 'nope'], ['email']],
    'website must be a url' => [['website' => 'nope'], ['website']],
    'industry must be a string' => [['industry' => 123], ['industry']],
    'industry must exist' => [['industry' => 'Unknown industry'], ['industry']],
    'type must be a string' => [['type' => 123], ['type']],
    'type must exist' => [['type' => 'Unknown type'], ['type']],
    'number_of_employees must be an integer' => [['number_of_employees' => 'many'], ['number_of_employees']],
    'domains must be valid' => [['domains' => ['not a domain']], ['domains.0']],
    'domains must not have a label ending with a hyphen' => [['domains' => ['acme-.test']], ['domains.0']],
    'domains must not have a label starting with a hyphen' => [['domains' => ['sub.-acme.test']], ['domains.0']],
    'domains must be distinct' => [['domains' => ['acme.test', 'acme.test']], ['domains.0', 'domains.1']],
    'is_contact_generation_enabled cannot be null' => [['is_contact_generation_enabled' => null], ['is_contact_generation_enabled']],
]);

it('requires a name that is not used by another organization', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    Organization::factory()->create(['name' => 'Taken']);
    $organization = Organization::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'taken'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('requires domains that are not used by another organization', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    Organization::factory()->create(['domains' => [['domain' => 'taken.test']]]);
    $organization = Organization::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['domains' => ['taken.test']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['domains.0']);
});

it('returns not found for a soft deleted organization', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();
    $organization->delete();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Changed'])
        ->assertNotFound();
});

it('can build its validation rules without a bound route, as the API documentation generator does', function (): void {
    expect((new UpdateOrganizationRequest())->rules())
        ->toHaveKeys(['name', 'domains', 'domains.*']);
});

it('requires domains that differ from those of another organization only by case', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    Organization::factory()->create(['domains' => [['domain' => 'Shared.edu']]]);
    $organization = Organization::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['domains' => ['shared.edu']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['domains.0']);
});

it('allows an organization to resubmit its own domains in a different case', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create(['domains' => [['domain' => 'Shared.edu']]]);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['domains' => ['shared.edu']])
        ->assertOk()
        ->assertJsonPath('data.domains', ['shared.edu']);
});

it('rejects a soft deleted industry or type', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();
    $originalIndustryId = $organization->industry_id;
    $originalTypeId = $organization->type_id;

    $industry = OrganizationIndustry::factory()->create();
    $type = OrganizationType::factory()->create();
    $industry->delete();
    $type->delete();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), [
        'industry' => $industry->name,
        'type' => $type->name,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['industry', 'type']);

    expect($organization->refresh()->only(['industry_id', 'type_id']))
        ->toBe(['industry_id' => $originalIndustryId, 'type_id' => $originalTypeId]);
});

it('finds the industry and type by name without regard to case', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();
    $industry = OrganizationIndustry::factory()->create(['name' => 'Higher Education']);
    $type = OrganizationType::factory()->create(['name' => 'Public']);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['industry' => 'higher education', 'type' => 'PUBLIC'])
        ->assertOk()
        ->assertJsonPath('data.industry.id', $industry->getKey())
        ->assertJsonPath('data.type.id', $type->getKey());

    expect($organization->refresh()->only(['industry_id', 'type_id']))
        ->toBe(['industry_id' => $industry->getKey(), 'type_id' => $type->getKey()]);
});

it('does not change the industry and type when they are not provided or when identifiers are sent', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->create();
    $original = $organization->only(['industry_id', 'type_id']);
    $other = OrganizationIndustry::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['industry_id' => $other->getKey(), 'type_id' => $other->getKey()])
        ->assertOk();

    expect($organization->refresh()->only(['industry_id', 'type_id']))->toBe($original);
});

it('returns not found for an archived organization before validating the request', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    $organization = Organization::factory()->archived()->create(['name' => 'Original']);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => ''])
        ->assertNotFound();

    expect($organization->refresh()->name)->toBe('Original');
});

it('checks permissions before returning not found for an archived organization', function (): void {
    $organization = Organization::factory()->archived()->create();

    $user = SystemUser::factory()->create();
    actingAsApiUser($user);

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'Changed'])
        ->assertForbidden();
});

it('explains that an archived organization reserves the name', function () use ($actingAsOrganizationUpdater): void {
    $actingAsOrganizationUpdater();

    Organization::factory()->archived()->create(['name' => 'Archived Org']);
    $organization = Organization::factory()->create();

    patchJson(route('api.v1.organizations.update', ['organization' => $organization], false), ['name' => 'ARCHIVED ORG'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.name', ['An archived organization with this name already exists.']);
});
