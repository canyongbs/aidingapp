<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Settings\PortalSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();
});

test('it returns unsuccessful response when no contact is authenticated', function () {
    $response = postJson(route('api.portal.logout'));

    $response
        ->assertOk()
        ->assertJsonPath('success', false);

    expect(PortalGuest::query()->count())->toBe(0);
});

test('it logs out contact, deletes portal token, and creates guest session', function () {
    $contact = Contact::factory()->create();

    $contact->createToken('knowledge-management-portal-access-token');
    $contact->createToken('some-other-token');

    actingAs($contact, 'contact');

    $response = postJson(route('api.portal.logout'));

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('redirect_url', route('portal.show'))
        ->assertSessionHas('guest_id');

    expect($contact->tokens()->where('name', 'knowledge-management-portal-access-token')->count())->toBe(0)
        ->and($contact->tokens()->where('name', 'some-other-token')->count())->toBe(1)
        ->and(auth('contact')->check())->toBeFalse()
        ->and(PortalGuest::query()->count())->toBe(1);
});

test('it preserves existing guest session during logout', function () {
    $contact = Contact::factory()->create();
    $existingGuest = PortalGuest::factory()->create();

    $contact->createToken('knowledge-management-portal-access-token');

    actingAs($contact, 'contact');

    session(['guest_id' => $existingGuest->getKey()]);

    $response = postJson(route('api.portal.logout'));

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertSessionHas('guest_id', $existingGuest->getKey());

    expect(PortalGuest::query()->count())->toBe(1);
});
