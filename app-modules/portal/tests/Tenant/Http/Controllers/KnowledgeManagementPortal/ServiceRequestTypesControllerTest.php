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

it('does not return categories that have no service request types', function () {
    $categoryWithType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->hasAttached($categoryWithType, relationship: 'categories')->create();

    $emptyCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($categoryWithType->id)
        ->and($categoryIds)->not->toContain($emptyCategory->id);
});

it('returns a category with a child category that has types', function () {
    $parentCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $childCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parentCategory->id]);
    ServiceRequestType::factory()->hasAttached($childCategory, relationship: 'categories')->create();

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($parentCategory->id);
});

it('does not return a parent category when all child categories are empty', function () {
    $parentCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestTypeCategory::factory()->create(['parent_id' => $parentCategory->id]);

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->not->toContain($parentCategory->id);
});

it('does not return archived service request types as part of a category', function () {
    $categoryWithOnlyArchivedType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->hasAttached($categoryWithOnlyArchivedType, relationship: 'categories')->create([
        'archived_at' => now(),
    ]);

    $categoryWithActiveType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->hasAttached($categoryWithActiveType, relationship: 'categories')->create();

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($categoryWithActiveType->id)
        ->and($categoryIds)->not->toContain($categoryWithOnlyArchivedType->id);
});

it('hides visibility restricted top level types from guests', function () {
    $visibleType = ServiceRequestType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach(ContactType::factory()->create());

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $typeIds = collect($response->json('types'))->pluck('id');

    expect($typeIds)->toContain($visibleType->id)
        ->and($typeIds)->not->toContain($restrictedType->id);
});

it('hides visibility restricted types from contacts of a non-matching contact type', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach($allowedContactType);

    $contact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    $response = actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $typeIds = collect($response->json('types'))->pluck('id');

    expect($typeIds)->not->toContain($restrictedType->id);
});

it('shows visibility restricted types to contacts of a matching contact type', function () {
    $allowedContactType = ContactType::factory()->create();

    $restrictedType = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $restrictedType->restrictedToContactTypes()->attach($allowedContactType);

    $contact = Contact::factory()->create(['type_id' => $allowedContactType->id]);

    $response = actingAs($contact, 'contact')
        ->getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $typeIds = collect($response->json('types'))->pluck('id');

    expect($typeIds)->toContain($restrictedType->id);
});

it('hides the entire subtree of a visibility restricted area from non-matching contacts', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $restrictedCategory = ServiceRequestTypeCategory::factory()->create([
        'parent_id' => null,
        'is_visibility_restricted' => true,
    ]);
    $restrictedCategory->restrictedToContactTypes()->attach($allowedContactType);
    ServiceRequestType::factory()->hasAttached($restrictedCategory, relationship: 'categories')->create();

    $nonMatchingContact = Contact::factory()->create(['type_id' => $otherContactType->id]);

    $hiddenResponse = actingAs($nonMatchingContact, 'contact')
        ->getJson(route('api.portal.service-request-type.index'));

    $hiddenResponse->assertOk();

    expect(collect($hiddenResponse->json('categories'))->pluck('id'))
        ->not->toContain($restrictedCategory->id);

    $matchingContact = Contact::factory()->create(['type_id' => $allowedContactType->id]);

    $visibleResponse = actingAs($matchingContact, 'contact')
        ->getJson(route('api.portal.service-request-type.index'));

    $visibleResponse->assertOk();

    expect(collect($visibleResponse->json('categories'))->pluck('id'))
        ->toContain($restrictedCategory->id);
});

it('lists a type under every category it belongs to', function () {
    $categoryA = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $categoryB = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$categoryA->id, $categoryB->id]);

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categories = collect($response->json('categories'));

    $categoryAData = $categories->firstWhere('id', $categoryA->id);
    $categoryBData = $categories->firstWhere('id', $categoryB->id);

    expect(collect($categoryAData['types'])->pluck('id'))->toContain($type->id)
        ->and(collect($categoryBData['types'])->pluck('id'))->toContain($type->id);

    // Each placement reports the category it is nested under.
    expect(collect($categoryAData['types'])->firstWhere('id', $type->id)['category_id'])->toBe($categoryA->id)
        ->and(collect($categoryBData['types'])->firstWhere('id', $type->id)['category_id'])->toBe($categoryB->id);
});

it('shows a type under an authorized category path and hides it under a restricted one', function () {
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

    $response = actingAs($contact, 'contact')->getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categories = collect($response->json('categories'));

    expect($categories->pluck('id'))->toContain($openCategory->id)
        ->and($categories->pluck('id'))->not->toContain($restrictedCategory->id);

    $openData = $categories->firstWhere('id', $openCategory->id);

    expect(collect($openData['types'])->pluck('id'))->toContain($type->id);
});

it('orders types within a category by their per-area sort rather than the global sort', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    // Global sort would place $first before $second, but the per-area pivot sort reverses them.
    $first = ServiceRequestType::factory()->create(['name' => 'First', 'sort' => 1]);
    $second = ServiceRequestType::factory()->create(['name' => 'Second', 'sort' => 2]);

    $category->types()->attach($first->id, ['sort' => 2]);
    $category->types()->attach($second->id, ['sort' => 1]);

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categoryData = collect($response->json('categories'))->firstWhere('id', $category->id);

    expect(collect($categoryData['types'])->pluck('id')->all())->toBe([$second->id, $first->id]);
});

it('orders the same type differently in each area it belongs to', function () {
    $categoryA = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $categoryB = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $shared = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 5]);
    $other = ServiceRequestType::factory()->create(['name' => 'Other', 'sort' => 6]);

    // Shared is first in A but last in B.
    $categoryA->types()->attach($shared->id, ['sort' => 1]);
    $categoryA->types()->attach($other->id, ['sort' => 2]);

    $categoryB->types()->attach($other->id, ['sort' => 1]);
    $categoryB->types()->attach($shared->id, ['sort' => 2]);

    $response = getJson(route('api.portal.service-request-type.index'));

    $response->assertOk();

    $categories = collect($response->json('categories'));

    expect(collect($categories->firstWhere('id', $categoryA->id)['types'])->pluck('id')->all())
        ->toBe([$shared->id, $other->id])
        ->and(collect($categories->firstWhere('id', $categoryB->id)['types'])->pluck('id')->all())
        ->toBe([$other->id, $shared->id]);
});
