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
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function (): void {
    $organization = Organization::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertOk();
});

it('returns the organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);

    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
        'is_contact_generation_enabled' => true,
    ]);

    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertOk()
        ->assertJsonPath('data.id', $organization->getKey())
        ->assertJsonPath('data.name', $organization->name)
        ->assertJsonPath('data.email', $organization->email)
        ->assertJsonPath('data.industry.id', $organization->industry_id)
        ->assertJsonPath('data.industry.name', $organization->industry->name)
        ->assertJsonPath('data.type.id', $organization->type_id)
        ->assertJsonPath('data.type.name', $organization->type->name)
        ->assertJsonPath('data.number_of_employees', $organization->number_of_employees)
        ->assertJsonPath('data.postalcode', $organization->postalcode)
        ->assertJsonPath('data.domains', ['example.com'])
        ->assertJsonPath('data.is_contact_generation_enabled', true);
});

it('skips stored domain entries that have no domain', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);

    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com'], ['domain' => null], ['domain' => ''], []],
    ]);

    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertOk()
        ->assertJsonPath('data.domains', ['example.com']);
});

it('returns null relations and an empty domain list when they are not set', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);

    $organization = Organization::factory()->create([
        'industry_id' => null,
        'type_id' => null,
        'domains' => null,
    ]);

    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertOk()
        ->assertJsonPath('data.industry', null)
        ->assertJsonPath('data.type', null)
        ->assertJsonPath('data.domains', []);
});

it('returns not found for a soft deleted organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');
    Sanctum::actingAs($user, ['api']);

    $organization = Organization::factory()->create();
    $organization->delete();

    getJson(route('api.v1.organizations.show', ['organization' => $organization], false))
        ->assertNotFound();
});
