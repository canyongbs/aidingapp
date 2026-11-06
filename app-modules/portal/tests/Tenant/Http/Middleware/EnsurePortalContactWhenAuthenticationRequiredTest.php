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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Advisory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\withToken;

beforeEach(function (): void {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->knowledge_management_portal_requires_authentication = true;
    $settings->save();
});

dataset('advisory routes', [
    'index' => fn (): string => route('api.portal.advisories'),
    'show' => fn (): string => route('api.portal.advisories.show', ['advisory' => Advisory::factory()->create()]),
]);

it('returns unauthorized to guests when authentication is required', function (string $url): void {
    getJson($url)->assertUnauthorized();
})->with('advisory routes');

it('allows a contact when authentication is required', function (string $url): void {
    $contact = Contact::factory()->create();

    actingAs($contact, 'contact');

    getJson($url)->assertOk();

    auth()->forgetGuards();

    withToken($contact->createToken('knowledge-management-portal-access-token')->plainTextToken)
        ->getJson($url)
        ->assertOk();
})->with('advisory routes');

it('allows guests when authentication is not required', function (string $url): void {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_requires_authentication = false;
    $settings->save();

    getJson($url)->assertOk();
})->with('advisory routes');
