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

use AidingApp\Contact\Models\ContactType;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

it('treats an unrestricted type with no categories as visible to anyone', function () {
    $type = ServiceRequestType::factory()->create();

    expect($type->isVisibleToContactType(null))->toBeTrue()
        ->and($type->isVisibleToContactType(ContactType::factory()->create()->id))->toBeTrue();
});

it('restricts a type with its own restriction to matching contact types', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $type = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $type->restrictedToContactTypes()->attach($allowedContactType);

    expect($type->isVisibleToContactType($allowedContactType->id))->toBeTrue()
        ->and($type->isVisibleToContactType($otherContactType->id))->toBeFalse()
        ->and($type->isVisibleToContactType(null))->toBeFalse();
});

it('is visible when at least one category path is authorized', function () {
    $otherContactType = ContactType::factory()->create();
    $allowedContactType = ContactType::factory()->create();

    $openCategory = ServiceRequestTypeCategory::factory()->create();

    $restrictedCategory = ServiceRequestTypeCategory::factory()->create(['is_visibility_restricted' => true]);
    $restrictedCategory->restrictedToContactTypes()->attach($allowedContactType);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$openCategory->id, $restrictedCategory->id]);

    // Reachable via the open category path.
    expect($type->isVisibleToContactType($otherContactType->id))->toBeTrue();
});

it('is hidden when every category path is restricted and none authorize the contact', function () {
    $otherContactType = ContactType::factory()->create();
    $allowedContactTypeA = ContactType::factory()->create();
    $allowedContactTypeB = ContactType::factory()->create();

    $restrictedCategoryA = ServiceRequestTypeCategory::factory()->create(['is_visibility_restricted' => true]);
    $restrictedCategoryA->restrictedToContactTypes()->attach($allowedContactTypeA);

    $restrictedCategoryB = ServiceRequestTypeCategory::factory()->create(['is_visibility_restricted' => true]);
    $restrictedCategoryB->restrictedToContactTypes()->attach($allowedContactTypeB);

    $type = ServiceRequestType::factory()->create();
    $type->categories()->attach([$restrictedCategoryA->id, $restrictedCategoryB->id]);

    expect($type->isVisibleToContactType($otherContactType->id))->toBeFalse()
        // Authorized for the path through category A.
        ->and($type->isVisibleToContactType($allowedContactTypeA->id))->toBeTrue();
});

it('is hidden when the type itself is restricted even under an open category', function () {
    $allowedContactType = ContactType::factory()->create();
    $otherContactType = ContactType::factory()->create();

    $openCategory = ServiceRequestTypeCategory::factory()->create();

    $type = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $type->restrictedToContactTypes()->attach($allowedContactType);
    $type->categories()->attach($openCategory->id);

    expect($type->isVisibleToContactType($otherContactType->id))->toBeFalse()
        ->and($type->isVisibleToContactType($allowedContactType->id))->toBeTrue();
});

it('walks the full category path when evaluating a nested placement', function () {
    $otherContactType = ContactType::factory()->create();
    $allowedContactType = ContactType::factory()->create();

    $restrictedParent = ServiceRequestTypeCategory::factory()->create(['is_visibility_restricted' => true]);
    $restrictedParent->restrictedToContactTypes()->attach($allowedContactType);

    $childOfRestricted = ServiceRequestTypeCategory::factory()->create(['parent_id' => $restrictedParent->id]);

    $openCategory = ServiceRequestTypeCategory::factory()->create();

    $onlyRestrictedPath = ServiceRequestType::factory()->create();
    $onlyRestrictedPath->categories()->attach($childOfRestricted->id);

    // The child is open, but its restricted parent blocks the whole path.
    expect($onlyRestrictedPath->isVisibleToContactType($otherContactType->id))->toBeFalse();

    $alsoOpenPath = ServiceRequestType::factory()->create();
    $alsoOpenPath->categories()->attach([$childOfRestricted->id, $openCategory->id]);

    // Still reachable through the open category.
    expect($alsoOpenPath->isVisibleToContactType($otherContactType->id))->toBeTrue();
});
