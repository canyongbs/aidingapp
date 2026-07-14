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

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();
});

it('blocks guests from loading the form of a visibility restricted type', function () {
    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach(ContactType::factory()->create());

    getJson(route('api.portal.service-request.create', ['type' => $restrictedType]))
        ->assertNotFound();
});

it('blocks contacts of a non-matching contact type from loading a restricted type form', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach($allowedContactType);

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request.create', ['type' => $restrictedType]))
        ->assertNotFound();
});

it('blocks a type nested under a restricted area from a non-matching contact', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedArea = ServiceRequestTypeCategory::factory()->create([
        'parent_id' => null,
        'is_visibility_restricted' => true,
    ]);
    $restrictedArea->restrictedToContactTypes()->attach($allowedContactType);

    $type = ServiceRequestType::factory()->hasAttached($restrictedArea, relationship: 'categories')->create();

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request.create', ['type' => $type]))
        ->assertNotFound();
});

it('builds breadcrumbs for the category the contact navigated under', function () {
    $categoryA = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $categoryB = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$categoryA->id, $categoryB->id]);

    $responseA = getJson(route('api.portal.service-request.create', ['type' => $type, 'category' => $categoryA->id]));
    $responseA->assertOk();
    expect($responseA->json('category.id'))->toBe($categoryA->id);

    $responseB = getJson(route('api.portal.service-request.create', ['type' => $type, 'category' => $categoryB->id]));
    $responseB->assertOk();
    expect($responseB->json('category.id'))->toBe($categoryB->id);
});

it('builds a nested breadcrumb ancestor trail for the navigated category', function () {
    $parent = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $child = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parent->id]);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach($child->id);

    $response = getJson(route('api.portal.service-request.create', ['type' => $type, 'category' => $child->id]));

    $response->assertOk();

    expect($response->json('category.id'))->toBe($child->id)
        ->and(collect($response->json('category.ancestors'))->pluck('id')->all())->toBe([$parent->id]);
});

it('allows loading the form when the type is reachable via any authorized category path', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $openCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $restrictedCategory = ServiceRequestTypeCategory::factory()->create([
        'parent_id' => null,
        'is_visibility_restricted' => true,
    ]);
    $restrictedCategory->restrictedToContactTypes()->attach($allowedContactType);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$openCategory->id, $restrictedCategory->id]);

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request.create', ['type' => $type]))
        ->assertOk();
});

it('does not build a breadcrumb for a category the type does not belong to', function () {
    $ownCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $unrelatedCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach($ownCategory->id);

    $response = getJson(route('api.portal.service-request.create', ['type' => $type, 'category' => $unrelatedCategory->id]));

    $response->assertOk();

    expect($response->json('category'))->toBeNull();
});

it('does not build a breadcrumb for a category the contact is not allowed to see', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $openCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $restrictedCategory = ServiceRequestTypeCategory::factory()->create([
        'parent_id' => null,
        'is_visibility_restricted' => true,
    ]);
    $restrictedCategory->restrictedToContactTypes()->attach($allowedContactType);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$openCategory->id, $restrictedCategory->id]);

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    // The type is still reachable through the open area, so the form loads, but the restricted
    // category must not be surfaced in the breadcrumb.
    $response = actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request.create', ['type' => $type, 'category' => $restrictedCategory->id]));

    $response->assertOk();

    expect($response->json('category'))->toBeNull();
});
