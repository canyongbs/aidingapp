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
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function (): void {
    $contact = Contact::factory()->create();

    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertUnauthorized();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertOk();
});

it('returns the contact', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.*.view']);
    Sanctum::actingAs($user, ['api']);

    $contact = Contact::factory()->create();

    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertOk()
        ->assertJsonPath('data.id', $contact->getKey())
        ->assertJsonPath('data.name', $contact->full_name)
        ->assertJsonPath('data.first_name', $contact->first_name)
        ->assertJsonPath('data.last_name', $contact->last_name)
        ->assertJsonPath('data.preferred_name', $contact->preferred)
        ->assertJsonPath('data.email', $contact->email)
        ->assertJsonPath('data.postal_code', $contact->postal)
        ->assertJsonPath('data.type.id', $contact->type_id)
        ->assertJsonPath('data.type.name', $contact->type->name)
        ->assertJsonPath('data.organization', null)
        ->assertJsonPath('data.is_managed', false);
});

it('returns not found for a deleted contact', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.*.view']);
    Sanctum::actingAs($user, ['api']);

    $contact = Contact::factory()->create();
    $contact->delete();

    getJson(route('api.v1.contacts.show', ['contact' => $contact], false))
        ->assertNotFound();
});
