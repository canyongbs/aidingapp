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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

$actingAsContactCreator = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.create']);
    Sanctum::actingAs($user, ['api']);

    return $user;
};

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () use ($actingAsContactCreator): void {
    $payload = ['first_name' => 'Jane', 'last_name' => 'Doe', 'type_id' => ContactType::factory()->create()->getKey()];

    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertUnauthorized();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $actingAsContactCreator();
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertCreated();
});

it('does not reveal validation details to unauthorized users', function (): void {
    $existing = Contact::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.contacts.store', [], false), ['email' => $existing->email])
        ->assertForbidden();
});

it('creates a contact with the provided attributes', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $type = ContactType::factory()->create();
    $organization = Organization::factory()->create();

    $response = postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'preferred_name' => 'JD',
        'email' => 'jane.doe@example.com',
        'type_id' => $type->getKey(),
        'organization_id' => $organization->getKey(),
        'postal_code' => '12345',
        'job_title' => 'Engineer',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'name', 'first_name', 'last_name', 'email', 'type' => ['id', 'name'], 'organization', 'is_managed']])
        ->assertJsonPath('data.name', 'Jane Doe')
        ->assertJsonPath('data.preferred_name', 'JD')
        ->assertJsonPath('data.postal_code', '12345')
        ->assertJsonPath('data.type.id', $type->getKey())
        ->assertJsonPath('data.organization.id', $organization->getKey());

    assertDatabaseHas(Contact::class, [
        'id' => $response['data']['id'],
        'full_name' => 'Jane Doe',
        'preferred' => 'JD',
        'postal' => '12345',
        'job_title' => 'Engineer',
        'organization_id' => $organization->getKey(),
    ]);
});

it('uses the default contact type when none is provided', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    ContactType::factory()->create(['is_default' => false]);
    $default = ContactType::factory()->create(['is_default' => true]);

    postJson(route('api.v1.contacts.store', [], false), ['first_name' => 'Jane', 'last_name' => 'Doe'])
        ->assertCreated()
        ->assertJsonPath('data.type.id', $default->getKey());
});

it('requires a contact type when no default is available', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    postJson(route('api.v1.contacts.store', [], false), ['first_name' => 'Jane', 'last_name' => 'Doe'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type_id']);

    assertDatabaseMissing(Contact::class, ['first_name' => 'Jane']);
});

it('validates the request', function (array $payload, array $errors) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $existing = Contact::factory()->create(['email' => 'taken@example.com']);
    Contact::factory()->create()->delete();

    $payload = array_merge(['first_name' => 'Jane', 'last_name' => 'Doe', 'type_id' => ContactType::factory()->create()->getKey()], $payload);

    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(Contact::query()->count())->toBe(1)
        ->and($existing->exists)->toBeTrue();
})->with([
    'first name is required' => [['first_name' => null], ['first_name']],
    'last name is required' => [['last_name' => null], ['last_name']],
    'email must be valid' => [['email' => 'not-an-email'], ['email']],
    'email must be unique' => [['email' => 'taken@example.com'], ['email']],
    'type must exist' => [['type_id' => '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11'], ['type_id']],
    'organization must exist' => [['organization_id' => '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11'], ['organization_id']],
    'attributes are limited to 255 characters' => [['job_title' => str_repeat('a', 256)], ['job_title']],
]);

it('allows an email address belonging to a deleted contact', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    Contact::factory()->create(['email' => 'reused@example.com'])->delete();

    postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'reused@example.com',
        'type_id' => ContactType::factory()->create()->getKey(),
    ])->assertCreated();
});
