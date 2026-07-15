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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();
});

function widgetFormRequestAsContact(Contact $contact, ServiceRequestType $type): TestResponse
{
    $contact->createToken('assistant-widget-access-token');

    actingAs($contact, 'contact');

    return getJson(
        route('widgets.assistant.api.service-request-form', ['type' => $type]),
        ['Origin' => config('app.url')],
    );
}

it('blocks widget contacts of a non-matching contact type from loading a restricted type form', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach($allowedContactType);

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    widgetFormRequestAsContact($contact, $restrictedType)
        ->assertNotFound();
});

it('allows widget contacts of a matching contact type to load a restricted type form', function () {
    $allowedContactType = ContactType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach($allowedContactType);

    $contact = Contact::factory()->create(['type_id' => $allowedContactType->id]);

    widgetFormRequestAsContact($contact, $restrictedType)
        ->assertOk();
});

it('blocks a widget type nested under a restricted area from a non-matching contact', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedArea = ServiceRequestTypeCategory::factory()->create([
        'parent_id' => null,
        'is_visibility_restricted' => true,
    ]);
    $restrictedArea->restrictedToContactTypes()->attach($allowedContactType);

    $type = ServiceRequestType::factory()->hasAttached($restrictedArea, relationship: 'categories')->create();

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    widgetFormRequestAsContact($contact, $type)
        ->assertNotFound();
});
