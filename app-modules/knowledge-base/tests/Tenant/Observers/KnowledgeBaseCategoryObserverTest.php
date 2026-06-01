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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;

it('auto assigns sort when creating a parent category without sort', function () {
    $category1 = KnowledgeBaseCategory::factory()->create();
    $category2 = KnowledgeBaseCategory::factory()->create();
    $category3 = KnowledgeBaseCategory::factory()->create();

    expect($category1->fresh()->sort)->toBe(1);
    expect($category2->fresh()->sort)->toBe(2);
    expect($category3->fresh()->sort)->toBe(3);
});

it('auto assigns sort when creating a sub category without sort', function () {
    $parent = KnowledgeBaseCategory::factory()->create();

    $sub1 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);
    $sub2 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);
    $sub3 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);

    expect($sub1->fresh()->sort)->toBe(1);
    expect($sub2->fresh()->sort)->toBe(2);
    expect($sub3->fresh()->sort)->toBe(3);
});

it('does not override sort when explicitly provided', function () {
    $category = KnowledgeBaseCategory::factory()->create(['sort' => 10]);

    expect($category->fresh()->sort)->toBe(10);
});

it('scopes sort independently for parent and sub categories', function () {
    $parent1 = KnowledgeBaseCategory::factory()->create();
    $parent2 = KnowledgeBaseCategory::factory()->create();

    $sub1OfParent1 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent1->id]);
    $sub1OfParent2 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent2->id]);
    $sub2OfParent1 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent1->id]);

    // Parent categories have their own sort sequence
    expect($parent1->fresh()->sort)->toBe(1);
    expect($parent2->fresh()->sort)->toBe(2);

    // Sub categories under parent1 have their own sort sequence
    expect($sub1OfParent1->fresh()->sort)->toBe(1);
    expect($sub2OfParent1->fresh()->sort)->toBe(2);

    // Sub categories under parent2 have their own sort sequence
    expect($sub1OfParent2->fresh()->sort)->toBe(1);
});

it('returns sub categories ordered by sort', function () {
    $parent = KnowledgeBaseCategory::factory()->create();

    $sub3 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id, 'sort' => 3]);
    $sub1 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id, 'sort' => 1]);
    $sub2 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id, 'sort' => 2]);

    $subCategories = $parent->subCategories()->get();

    expect($subCategories->pluck('id')->toArray())
        ->toBe([$sub1->id, $sub2->id, $sub3->id]);
});

it('assigns sort correctly after soft-deleted categories exist', function () {
    $category1 = KnowledgeBaseCategory::factory()->create();
    $category2 = KnowledgeBaseCategory::factory()->create();

    $category2->delete();

    $category3 = KnowledgeBaseCategory::factory()->create();

    // sort should be max(sort) + 1, which accounts for the soft-deleted record's sort value
    expect($category1->fresh()->sort)->toBe(1);
    expect($category3->fresh()->sort)->toBe(3);
});

it('assigns sort correctly for sub categories after some are soft-deleted', function () {
    $parent = KnowledgeBaseCategory::factory()->create();

    $sub1 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);
    $sub2 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);

    $sub2->delete();

    $sub3 = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);

    expect($sub1->fresh()->sort)->toBe(1);
    expect($sub3->fresh()->sort)->toBe(3);
});

it('assigns sort based on max value when non-sequential sort values exist', function () {
    $parent = KnowledgeBaseCategory::factory()->create();

    KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id, 'sort' => 5]);
    KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id, 'sort' => 10]);

    $newSub = KnowledgeBaseCategory::factory()->create(['parent_id' => $parent->id]);

    expect($newSub->fresh()->sort)->toBe(11);
});
