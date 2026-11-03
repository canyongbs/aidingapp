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

use function Pest\Laravel\getJson;

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function (): void {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.index', [], false))
        ->assertOk();
});

it('returns a paginated list of organizations', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->count(3)->create();

    $response = getJson(route('api.v1.organizations.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'email',
                'phone_number',
                'website',
                'industry' => ['id', 'name'],
                'type' => ['id', 'name'],
                'description',
                'number_of_employees',
                'address',
                'city',
                'state',
                'postalcode',
                'country',
                'linkedin_url',
                'facebook_url',
                'twitter_url',
                'domains',
                'is_contact_generation_enabled',
                'created_at',
                'updated_at',
            ],
        ],
        'links',
        'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('returns domains as a flat list of strings', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create([
        'domains' => [['domain' => 'example.com'], ['domain' => 'example.org']],
    ]);

    getJson(route('api.v1.organizations.index', [], false))
        ->assertOk()
        ->assertJsonPath('data.0.domains', ['example.com', 'example.org']);
});

it('does not return soft deleted organizations', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create();
    Organization::factory()->create()->delete();

    expect(getJson(route('api.v1.organizations.index', [], false))['meta']['total'])
        ->toBe(1);
});

it('can filter organizations by name', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create(['name' => 'Acme Corporation']);
    Organization::factory()->count(2)->create();

    $response = getJson(route('api.v1.organizations.index', ['filter' => ['name' => 'Acme']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['name'])->toBe('Acme Corporation');
});

it('can filter organizations by email', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create(['email' => 'hello@acme.test']);
    Organization::factory()->count(2)->create(['email' => 'info@other.test']);

    $response = getJson(route('api.v1.organizations.index', ['filter' => ['email' => 'acme']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['email'])->toBe('hello@acme.test');
});

it('can filter organizations by industry', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    $industry = OrganizationIndustry::factory()->create();
    Organization::factory()->create(['industry_id' => $industry->getKey()]);
    Organization::factory()->count(2)->create();

    $response = getJson(route('api.v1.organizations.index', ['filter' => ['industry' => $industry->getKey()]], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['industry']['id'])->toBe($industry->getKey());
});

it('can filter organizations by type', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    $type = OrganizationType::factory()->create();
    Organization::factory()->create(['type_id' => $type->getKey()]);
    Organization::factory()->count(2)->create();

    $response = getJson(route('api.v1.organizations.index', ['filter' => ['type' => $type->getKey()]], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['type']['id'])->toBe($type->getKey());
});

it('can filter organizations by whether contact generation is enabled', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create(['is_contact_generation_enabled' => true]);
    Organization::factory()->count(2)->create(['is_contact_generation_enabled' => false]);

    $response = getJson(route('api.v1.organizations.index', ['filter' => ['is_contact_generation_enabled' => 'true']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['is_contact_generation_enabled'])->toBeTrue();
});

it('sorts organizations by name by default and supports descending sorts', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    Organization::factory()->create(['name' => 'Bravo']);
    Organization::factory()->create(['name' => 'Alpha']);
    Organization::factory()->create(['name' => 'Charlie']);

    expect(getJson(route('api.v1.organizations.index', [], false))->json('data.*.name'))
        ->toBe(['Alpha', 'Bravo', 'Charlie']);

    expect(getJson(route('api.v1.organizations.index', ['sort' => '-name'], false))->json('data.*.name'))
        ->toBe(['Charlie', 'Bravo', 'Alpha']);
});

it('rejects filters and sorts that are not allowed', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);

    getJson(route('api.v1.organizations.index', ['filter' => ['description' => 'x']], false))
        ->assertBadRequest();

    getJson(route('api.v1.organizations.index', ['sort' => 'description'], false))
        ->assertBadRequest();
});
