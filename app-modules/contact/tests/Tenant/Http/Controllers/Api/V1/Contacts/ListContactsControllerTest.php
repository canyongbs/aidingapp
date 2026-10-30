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

use function Pest\Laravel\getJson;

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function (): void {
    getJson(route('api.v1.contacts.index', [], false))
        ->assertUnauthorized();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.contacts.index', [], false))
        ->assertOk();
});

it('returns a paginated list of contacts', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);

    Contact::factory()->count(3)->create();

    $response = getJson(route('api.v1.contacts.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure(['data', 'links', 'meta']);

    expect($response['data'])->toHaveCount(3);
});

it('does not include deleted contacts', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);

    $contact = Contact::factory()->create();
    Contact::factory()->create()->delete();

    $response = getJson(route('api.v1.contacts.index', [], false));
    $response->assertOk();

    expect($response['data'])->toHaveCount(1)
        ->and($response['data'][0]['id'])->toBe($contact->getKey());
});

it('can filter contacts by name, email, type, and organization', function (string $filter): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);

    $type = ContactType::factory()->create();
    $organization = Organization::factory()->create();

    $included = Contact::factory()->create([
        'full_name' => 'Zebediah Quillfeather',
        'email' => 'zebediah@quillfeather.test',
        'type_id' => $type->getKey(),
        'organization_id' => $organization->getKey(),
    ]);

    Contact::factory()->count(2)->create();

    $value = [
        'name' => 'quillfeather',
        'email' => 'zebediah@',
        'type' => $type->getKey(),
        'organization' => $organization->getKey(),
    ][$filter];

    $response = getJson(route('api.v1.contacts.index', ['filter' => [$filter => $value]], false));
    $response->assertOk();

    expect($response['data'])->toHaveCount(1)
        ->and($response['data'][0]['id'])->toBe($included->getKey());
})->with(['name', 'email', 'type', 'organization']);

it('sorts contacts by name by default and supports descending sorts', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);

    Contact::factory()->create(['full_name' => 'Bravo Contact']);
    Contact::factory()->create(['full_name' => 'Alpha Contact']);
    Contact::factory()->create(['full_name' => 'Charlie Contact']);

    getJson(route('api.v1.contacts.index', [], false))
        ->assertJsonPath('data.*.name', ['Alpha Contact', 'Bravo Contact', 'Charlie Contact']);

    getJson(route('api.v1.contacts.index', ['sort' => '-name'], false))
        ->assertJsonPath('data.*.name', ['Charlie Contact', 'Bravo Contact', 'Alpha Contact']);
});
