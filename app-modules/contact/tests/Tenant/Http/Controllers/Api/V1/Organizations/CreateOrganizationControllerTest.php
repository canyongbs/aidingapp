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

use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

$actingAsOrganizationCreator = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');
    Sanctum::actingAs($user, ['api']);

    return $user;
};

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function (): void {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertForbidden();

    expect(Organization::query()->count())->toBe(0);
});

it('checks permissions before validating the request', function (): void {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.organizations.store', [], false), [])
        ->assertForbidden();
});

it('can create an organization with only a name', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Acme')
        ->assertJsonPath('data.domains', [])
        ->assertJsonPath('data.is_contact_generation_enabled', false);

    expect(Organization::query()->where('name', 'Acme')->exists())->toBeTrue();
});

it('can create an organization with all attributes', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    $industry = OrganizationIndustry::factory()->create();
    $type = OrganizationType::factory()->create();

    $response = postJson(route('api.v1.organizations.store', [], false), [
        'name' => 'Acme Corporation',
        'email' => 'hello@acme.test',
        'phone_number' => '+15555550100',
        'website' => 'https://acme.test',
        'industry' => $industry->name,
        'type' => $type->name,
        'description' => 'A company that makes everything.',
        'number_of_employees' => 250,
        'address' => '1 Road Runner Way',
        'city' => 'Phoenix',
        'state' => 'AZ',
        'postalcode' => '85001',
        'country' => 'United States',
        'linkedin_url' => 'https://linkedin.com/company/acme',
        'facebook_url' => 'https://facebook.com/acme',
        'twitter_url' => 'https://twitter.com/acme',
        'domains' => ['acme.test', 'acme.org'],
        'is_contact_generation_enabled' => true,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Acme Corporation')
        ->assertJsonPath('data.industry.id', $industry->getKey())
        ->assertJsonPath('data.type.id', $type->getKey())
        ->assertJsonPath('data.domains', ['acme.test', 'acme.org'])
        ->assertJsonPath('data.is_contact_generation_enabled', true);

    $organization = Organization::query()->findOrFail($response['data']['id']);

    expect($organization->only([
        'name', 'email', 'phone_number', 'website', 'industry_id', 'type_id', 'description', 'number_of_employees',
        'address', 'city', 'state', 'postalcode', 'country', 'linkedin_url', 'facebook_url', 'twitter_url',
        'domains', 'is_contact_generation_enabled',
    ]))->toBe([
        'name' => 'Acme Corporation',
        'email' => 'hello@acme.test',
        'phone_number' => '+15555550100',
        'website' => 'https://acme.test',
        'industry_id' => $industry->getKey(),
        'type_id' => $type->getKey(),
        'description' => 'A company that makes everything.',
        'number_of_employees' => 250,
        'address' => '1 Road Runner Way',
        'city' => 'Phoenix',
        'state' => 'AZ',
        'postalcode' => '85001',
        'country' => 'United States',
        'linkedin_url' => 'https://linkedin.com/company/acme',
        'facebook_url' => 'https://facebook.com/acme',
        'twitter_url' => 'https://twitter.com/acme',
        'domains' => [['domain' => 'acme.test'], ['domain' => 'acme.org']],
        'is_contact_generation_enabled' => true,
    ]);
});

it('uses the default industry and type when they are not provided', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    $industry = OrganizationIndustry::factory()->create(['is_default' => true]);
    $type = OrganizationType::factory()->create(['is_default' => true]);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertCreated()
        ->assertJsonPath('data.industry.id', $industry->getKey())
        ->assertJsonPath('data.type.id', $type->getKey());
});

it('does not use the default industry and type when null is explicitly provided', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    OrganizationIndustry::factory()->create(['is_default' => true]);
    OrganizationType::factory()->create(['is_default' => true]);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'industry' => null, 'type' => null])
        ->assertCreated()
        ->assertJsonPath('data.industry', null)
        ->assertJsonPath('data.type', null);
});

it('normalizes domains to lowercase', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'domains' => [' Acme.TEST ']])
        ->assertCreated()
        ->assertJsonPath('data.domains', ['acme.test']);
});

it('validates the request', function (array $data, array $errors) use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    postJson(route('api.v1.organizations.store', [], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(Organization::query()->count())->toBe(0);
})->with([
    'name is required' => [[], ['name']],
    'name must be a string' => [['name' => 123], ['name']],
    'name is too long' => [['name' => str_repeat('a', 256)], ['name']],
    'email must be an email' => [['name' => 'Acme', 'email' => 'nope'], ['email']],
    'website must be a url' => [['name' => 'Acme', 'website' => 'nope'], ['website']],
    'linkedin_url must be a url' => [['name' => 'Acme', 'linkedin_url' => 'nope'], ['linkedin_url']],
    'facebook_url must be a url' => [['name' => 'Acme', 'facebook_url' => 'nope'], ['facebook_url']],
    'twitter_url must be a url' => [['name' => 'Acme', 'twitter_url' => 'nope'], ['twitter_url']],
    'industry must be a string' => [['name' => 'Acme', 'industry' => 123], ['industry']],
    'industry is too long' => [['name' => 'Acme', 'industry' => str_repeat('a', 256)], ['industry']],
    'industry must exist' => [['name' => 'Acme', 'industry' => 'Unknown industry'], ['industry']],
    'type must be a string' => [['name' => 'Acme', 'type' => 123], ['type']],
    'type must exist' => [['name' => 'Acme', 'type' => 'Unknown type'], ['type']],
    'number_of_employees must be an integer' => [['name' => 'Acme', 'number_of_employees' => 'many'], ['number_of_employees']],
    'number_of_employees must not be negative' => [['name' => 'Acme', 'number_of_employees' => -1], ['number_of_employees']],
    'domains must be an array' => [['name' => 'Acme', 'domains' => 'acme.test'], ['domains']],
    'domains must be valid' => [['name' => 'Acme', 'domains' => ['not a domain']], ['domains.0']],
    'domains must not have a label ending with a hyphen' => [['name' => 'Acme', 'domains' => ['acme-.test']], ['domains.0']],
    'domains must not have a label starting with a hyphen' => [['name' => 'Acme', 'domains' => ['sub.-acme.test']], ['domains.0']],
    'domains must be distinct' => [['name' => 'Acme', 'domains' => ['acme.test', 'ACME.test']], ['domains.0', 'domains.1']],
    'is_contact_generation_enabled must be a boolean' => [['name' => 'Acme', 'is_contact_generation_enabled' => 'maybe'], ['is_contact_generation_enabled']],
]);

it('requires a unique name', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    Organization::factory()->create(['name' => 'Acme']);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'acme'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('allows the name of a soft deleted organization to be reused', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    Organization::factory()->create(['name' => 'Acme', 'domains' => null])->delete();

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme'])
        ->assertCreated();
});

it('requires domains that are not used by another organization', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    Organization::factory()->create(['domains' => [['domain' => 'acme.test']]]);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'domains' => ['acme.test']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['domains.0']);
});

it('requires domains that differ from those of another organization only by case', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    Organization::factory()->create(['domains' => [['domain' => 'Shared.edu']]]);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'domains' => ['shared.edu']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['domains.0']);
});

it('rejects a soft deleted industry or type', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    $industry = OrganizationIndustry::factory()->create();
    $type = OrganizationType::factory()->create();
    $industry->delete();
    $type->delete();

    postJson(route('api.v1.organizations.store', [], false), [
        'name' => 'Acme',
        'industry' => $industry->name,
        'type' => $type->name,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['industry', 'type']);

    expect(Organization::query()->count())->toBe(0);
});

it('finds the industry and type by name without regard to case', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    $industry = OrganizationIndustry::factory()->create(['name' => 'Higher Education']);
    $type = OrganizationType::factory()->create(['name' => 'Public']);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'industry' => 'higher education', 'type' => 'PUBLIC'])
        ->assertCreated()
        ->assertJsonPath('data.industry.id', $industry->getKey())
        ->assertJsonPath('data.industry.name', 'Higher Education')
        ->assertJsonPath('data.type.id', $type->getKey())
        ->assertJsonPath('data.type.name', 'Public');
});

it('does not accept industry and type identifiers', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    $industry = OrganizationIndustry::factory()->create();
    $type = OrganizationType::factory()->create();

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'industry_id' => $industry->getKey(), 'type_id' => $type->getKey()])
        ->assertCreated()
        ->assertJsonPath('data.industry', null)
        ->assertJsonPath('data.type', null);
});

it('can use the name of an industry or type that shares its name with a deleted one', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    OrganizationIndustry::factory()->create(['name' => 'Retail'])->delete();
    $industry = OrganizationIndustry::factory()->create(['name' => 'Retail']);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'Acme', 'industry' => 'Retail'])
        ->assertCreated()
        ->assertJsonPath('data.industry.id', $industry->getKey());
});

it('explains that an archived organization reserves the name', function () use ($actingAsOrganizationCreator): void {
    $actingAsOrganizationCreator();

    Organization::factory()->archived()->create(['name' => 'Archived Org']);

    postJson(route('api.v1.organizations.store', [], false), ['name' => 'ARCHIVED ORG'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.name', ['An archived organization with this name already exists.']);
});
