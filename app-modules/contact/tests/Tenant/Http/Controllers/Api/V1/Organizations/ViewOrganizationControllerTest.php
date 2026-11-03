<?php

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
