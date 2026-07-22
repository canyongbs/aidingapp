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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

/**
 * @param array<int, array<string, mixed>> $categories
 * @param array<int, array<string, mixed>> $uncategorizedTypes
 *
 * @return array<string, mixed>
 */
function multipleCategoriesTreeData(array $categories = [], array $uncategorizedTypes = []): array
{
    return [
        'categories' => $categories,
        'uncategorized_types' => $uncategorizedTypes,
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [],
    ];
}

/**
 * @param array<int, array<string, mixed>> $types
 *
 * @return array<string, mixed>
 */
function categoryNode(ServiceRequestTypeCategory $category, array $types = [], int $sort = 1): array
{
    return [
        'id' => $category->id,
        'name' => $category->name,
        'type' => 'category',
        'sort' => $sort,
        'parent_id' => $category->parent_id,
        'children' => [],
        'types' => $types,
    ];
}

/**
 * @return array<string, mixed>
 */
function typeNode(ServiceRequestType $type, ServiceRequestTypeCategory $category, int $sort): array
{
    return [
        'id' => $type->id,
        'name' => $type->name,
        'type' => 'type',
        'sort' => $sort,
    ];
}

beforeEach(function () {
    asSuperAdmin();
});

it('files a type under every category it appears in', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->hasAttached($catA, relationship: 'categories')->create(['name' => 'Shared', 'sort' => 1]);

    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [typeNode($type, $catA, 1)], 1),
        categoryNode($catB, [typeNode($type, $catB, 1)], 2),
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect($type->fresh()->categories->pluck('id')->sort()->values()->all())
        ->toBe(collect([$catA->id, $catB->id])->sort()->values()->all());
});

it('stores a distinct per-area sort on each membership', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $shared = ServiceRequestType::factory()->hasAttached($catA, relationship: 'categories')->create(['name' => 'Shared', 'sort' => 1]);
    $other = ServiceRequestType::factory()->hasAttached($catB, relationship: 'categories')->create(['name' => 'Other', 'sort' => 1]);

    // In A the shared type is first (sort 1); in B it sits after "Other" (sort 2).
    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [typeNode($shared, $catA, 1)], 1),
        categoryNode($catB, [
            typeNode($other, $catB, 1),
            typeNode($shared, $catB, 2),
        ], 2),
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    $shared = $shared->fresh();

    expect($shared->categories->firstWhere('id', $catA->id)->pivot->sort)->toBe(1);
    expect($shared->categories->firstWhere('id', $catB->id)->pivot->sort)->toBe(2);
});

it('detaches a single membership when a type is removed from one of its categories', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    // Save with the type only present under A.
    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [typeNode($type, $catA, 1)], 1),
        categoryNode($catB, [], 2),
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect($type->fresh()->categories->pluck('id')->all())->toBe([$catA->id]);
});

it('detaches every membership when a type becomes uncategorized', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Loose', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    $treeData = multipleCategoriesTreeData(
        categories: [
            categoryNode($catA, [], 1),
            categoryNode($catB, [], 2),
        ],
        uncategorizedTypes: [
            [
                'id' => $type->id,
                'name' => $type->name,
                'type' => 'type',
                'sort' => 1,
            ],
        ],
    );

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect($type->fresh()->categories)->toBeEmpty();
});

it('keeps a shared type when one of its categories is deleted', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [typeNode($type, $catA, 1)], 1),
    ]);
    $treeData['deleted_categories'] = [$catB->id];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect(ServiceRequestType::find($type->id))->not->toBeNull();
    expect($type->fresh()->categories->pluck('id')->all())->toBe([$catA->id]);
    expect(ServiceRequestTypeCategory::find($catB->id))->toBeNull();
});

it('orders types within a category by their per-area sort when reading the tree', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Area', 'sort' => 1]);

    $first = ServiceRequestType::factory()->create(['name' => 'First', 'sort' => 1]);
    $second = ServiceRequestType::factory()->create(['name' => 'Second', 'sort' => 1]);

    // Attach in an order that does not match the desired per-area sort.
    $category->types()->attach($second->id, ['sort' => 2]);
    $category->types()->attach($first->id, ['sort' => 1]);

    $data = livewire(ListServiceRequestTypes::class)
        ->instance()
        ->getHierarchicalData();

    $types = $data['categories'][0]['types'];

    expect(array_column($types, 'id'))->toBe([$first->id, $second->id]);

    // The emitted sort reflects the per-area pivot sort, not the type's global sort.
    expect(collect($types)->firstWhere('id', $first->id)['sort'])->toBe(1)
        ->and(collect($types)->firstWhere('id', $second->id)['sort'])->toBe(2);
});

it('stores the per-area sort for a newly created type', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Area', 'sort' => 1]);

    $first = ServiceRequestType::factory()->hasAttached($category, relationship: 'categories')->create(['name' => 'First', 'sort' => 1]);
    $second = ServiceRequestType::factory()->hasAttached($category, relationship: 'categories')->create(['name' => 'Second', 'sort' => 2]);

    // The new type sits third in the area, so its per-area sort is derived from that position.
    $treeData = multipleCategoriesTreeData([
        [
            'id' => $category->id,
            'name' => $category->name,
            'type' => 'category',
            'sort' => 1,
            'parent_id' => null,
            'children' => [],
            'types' => [
                typeNode($first, $category, 1),
                typeNode($second, $category, 2),
                [
                    'id' => 'temp_1',
                    'name' => 'Brand New',
                    'type' => 'type',
                    'sort' => 3,
                ],
            ],
        ],
    ]);
    $treeData['new_types'] = [
        [
            'temp_id' => 'temp_1',
            'name' => 'Brand New',
            'sort' => 3,
        ],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    $type = ServiceRequestType::query()->where('name', 'Brand New')->firstOrFail();

    expect($type->categories->pluck('id')->all())->toBe([$category->id]);
    expect($type->categories->firstWhere('id', $category->id)->pivot->sort)->toBe(3);
});

it('creates a brand new type once and files it under every area it appears in', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $newTypeNode = fn (ServiceRequestTypeCategory $category, int $sort): array => [
        'id' => 'temp_1',
        'name' => 'Fresh',
        'type' => 'type',
        'sort' => $sort,
    ];

    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [$newTypeNode($catA, 1)], 1),
        categoryNode($catB, [$newTypeNode($catB, 1)], 2),
    ]);
    // The tree references the same temp id twice; new_types carries duplicate entries to prove the
    // backend creates the type only once and applies both placements.
    $treeData['new_types'] = [
        ['temp_id' => 'temp_1', 'name' => 'Fresh', 'sort' => 1],
        ['temp_id' => 'temp_1', 'name' => 'Fresh', 'sort' => 1],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    $types = ServiceRequestType::query()->where('name', 'Fresh')->get();

    expect($types)->toHaveCount(1);
    expect($types->first()->categories->pluck('id')->sort()->values()->all())
        ->toBe(collect([$catA->id, $catB->id])->sort()->values()->all());
});

it('creates a brand new uncategorized type once with no memberships', function () {
    $treeData = multipleCategoriesTreeData(
        categories: [],
        uncategorizedTypes: [
            [
                'id' => 'temp_1',
                'name' => 'Loner',
                'type' => 'type',
                'sort' => 1,
            ],
        ],
    );
    $treeData['new_types'] = [
        ['temp_id' => 'temp_1', 'name' => 'Loner', 'sort' => 1],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    $type = ServiceRequestType::query()->where('name', 'Loner')->firstOrFail();

    expect($type->categories)->toBeEmpty();
});

it('rejects saving two brand new types that share a name across different areas', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $treeData = multipleCategoriesTreeData([
        categoryNode($catA, [[
            'id' => 'temp_1',
            'name' => 'Duplicate',
            'type' => 'type',
            'sort' => 1,
        ]], 1),
        categoryNode($catB, [[
            'id' => 'temp_2',
            'name' => 'Duplicate',
            'type' => 'type',
            'sort' => 1,
        ]], 2),
    ]);
    $treeData['new_types'] = [
        ['temp_id' => 'temp_1', 'name' => 'Duplicate', 'sort' => 1],
        ['temp_id' => 'temp_2', 'name' => 'Duplicate', 'sort' => 1],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertReturned(false)
        ->assertNotified('Unable to save changes');

    expect(ServiceRequestType::query()->where('name', 'Duplicate')->count())->toBe(0);
});

it('rejects a brand new type whose name matches an existing type', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);

    ServiceRequestType::factory()->create(['name' => 'Existing']);

    $treeData = multipleCategoriesTreeData([
        categoryNode($category, [[
            'id' => 'temp_1',
            'name' => 'Existing',
            'type' => 'type',
            'sort' => 1,
        ]], 1),
    ]);
    $treeData['new_types'] = [
        ['temp_id' => 'temp_1', 'name' => 'Existing', 'sort' => 1],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertReturned(false)
        ->assertNotified('Unable to save changes');

    expect(ServiceRequestType::query()->where('name', 'Existing')->count())->toBe(1);
});

it('returns true and saves when there are no name collisions', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);

    $treeData = multipleCategoriesTreeData([
        categoryNode($category, [[
            'id' => 'temp_1',
            'name' => 'Unique Name',
            'type' => 'type',
            'sort' => 1,
        ]], 1),
    ]);
    $treeData['new_types'] = [
        ['temp_id' => 'temp_1', 'name' => 'Unique Name', 'sort' => 1],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertReturned(true)
        ->assertHasNoErrors();

    expect(ServiceRequestType::query()->where('name', 'Unique Name')->count())->toBe(1);
});
