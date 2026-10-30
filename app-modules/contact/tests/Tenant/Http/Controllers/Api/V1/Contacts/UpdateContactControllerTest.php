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
use AidingApp\Contact\Services\ManagedContactService;
use App\Models\SystemUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;

$actingAsContactUpdater = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.*.update']);
    Sanctum::actingAs($user, ['api']);

    return $user;
};

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () use ($actingAsContactUpdater): void {
    $contact = Contact::factory()->create();
    $payload = ['job_title' => 'Updated'];

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertUnauthorized();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertForbidden();

    $actingAsContactUpdater();
    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertOk();
});

it('updates only the attributes included in the request', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = Contact::factory()->create(['job_title' => 'Original', 'city' => 'Springfield']);

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), [
        'job_title' => 'Updated',
        'postal_code' => '54321',
        'preferred_name' => null,
    ])
        ->assertOk()
        ->assertJsonPath('data.id', $contact->getKey())
        ->assertJsonPath('data.job_title', 'Updated')
        ->assertJsonPath('data.postal_code', '54321')
        ->assertJsonPath('data.preferred_name', null)
        ->assertJsonPath('data.city', 'Springfield');

    $fresh = $contact->fresh();

    expect($fresh->job_title)->toBe('Updated')
        ->and($fresh->postal)->toBe('54321')
        ->and($fresh->preferred)->toBeNull()
        ->and($fresh->city)->toBe('Springfield')
        ->and($fresh->full_name)->toBe($contact->full_name);
});

it('keeps the full name in sync with the first and last name', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = Contact::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'full_name' => 'Jane Doe']);

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), ['last_name' => 'Smith'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Jane Smith');

    assertDatabaseHas(Contact::class, ['id' => $contact->getKey(), 'full_name' => 'Jane Smith']);
});

it('can change the contact type', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = Contact::factory()->create();
    $type = ContactType::factory()->create();

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), ['type_id' => $type->getKey()])
        ->assertOk()
        ->assertJsonPath('data.type.id', $type->getKey());
});

it('can change the organization', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = Contact::factory()->create();
    $organization = Organization::factory()->create();

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), ['organization_id' => $organization->getKey()])
        ->assertOk()
        ->assertJsonPath('data.organization.id', $organization->getKey());
});

it('validates the request', function (array $payload, array $errors) use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    Contact::factory()->create(['email' => 'taken@example.com']);
    $contact = Contact::factory()->create();

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);
})->with([
    'first name cannot be blank' => [['first_name' => ''], ['first_name']],
    'last name cannot be null' => [['last_name' => null], ['last_name']],
    'email must be valid' => [['email' => 'not-an-email'], ['email']],
    'email must be unique' => [['email' => 'taken@example.com'], ['email']],
    'type cannot be null' => [['type_id' => null], ['type_id']],
    'type must exist' => [['type_id' => '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11'], ['type_id']],
]);

it('allows a contact to keep its own email address', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = Contact::factory()->create(['email' => 'same@example.com']);

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), ['email' => 'same@example.com', 'job_title' => 'Updated'])
        ->assertOk();
});

it('cannot update a managed contact', function () use ($actingAsContactUpdater): void {
    $actingAsContactUpdater();

    $contact = app(ManagedContactService::class)->enable(User::factory()->create(), ContactType::factory()->create()->getKey());

    patchJson(route('api.v1.contacts.update', ['contact' => $contact], false), ['job_title' => 'Updated'])
        ->assertForbidden();

    expect($contact->fresh()->job_title)->not->toBe('Updated');
});
