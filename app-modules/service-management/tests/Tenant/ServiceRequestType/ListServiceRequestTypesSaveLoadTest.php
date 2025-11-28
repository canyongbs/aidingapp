<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

</COPYRIGHT>
*/

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();
});

it('loads hierarchical data', function () {
    $root = ServiceRequestTypeCategory::factory()->create(['name' => 'Root', 'sort' => 1]);
    $child = ServiceRequestTypeCategory::factory()->create(['name' => 'Child', 'sort' => 1, 'parent_id' => $root->id]);

    $type1 = ServiceRequestType::factory()->create(['name' => 'Type 1', 'category_id' => $root->id, 'sort' => 1]);
    $type2 = ServiceRequestType::factory()->create(['name' => 'Type 2', 'category_id' => $child->id, 'sort' => 1]);

    $component = livewire(ListServiceRequestTypes::class);

    $component->assertSuccessful();

    $data = $component->instance()->getHierarchicalData();

    expect($data['categories'])->toBeArray();
    expect(count($data['categories']))->toBe(1);

    $first = $data['categories'][0];

    expect($first['id'])->toBe($root->id);
    expect(count($first['types']))->toBe(1);
    expect($first['children'][0]['id'])->toBe($child->id);
});

it('saves new category and moves existing type into it in one operation', function () {
    // Existing category and type
    $existingCategory = ServiceRequestTypeCategory::factory()->create(['name' => 'Existing', 'sort' => 1]);
    $type = ServiceRequestType::factory()->create(['name' => 'Movable', 'category_id' => null, 'sort' => 1]);

    // Prepare payload: create new category (temp) and move type into it
    $tempId = 'temp_1';

    $treeData = [
        'categories' => [
            [
                'id' => $tempId,
                'name' => 'New Cat',
                'type' => 'category',
                'sort' => 1,
                'parent_id' => null,
                'children' => [],
                'types' => [
                    [
                        'id' => $type->id,
                        'name' => $type->name,
                        'type' => 'type',
                        'sort' => 1,
                        'category_id' => $tempId,
                    ],
                ],
            ],
        ],
        'uncategorized_types' => [],
        'new_categories' => [
            [
                'temp_id' => $tempId,
                'name' => 'New Cat',
                'parent_id' => null,
                'sort' => 1,
            ],
        ],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    // After save, the type should be assigned to the new category
    $freshType = $type->fresh();

    expect($freshType->category_id)->not->toBeNull();

    $newCategory = ServiceRequestTypeCategory::find($freshType->category_id);

    expect($newCategory)->not->toBeNull();
    expect($newCategory->name)->toBe('New Cat');
});

it('moves type from one existing category to another existing category', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'ToMove', 'category_id' => $catA->id, 'sort' => 1]);

    $treeData = [
        'categories' => [
            [
                'id' => $catB->id,
                'name' => $catB->name,
                'type' => 'category',
                'sort' => 1,
                'parent_id' => null,
                'children' => [],
                'types' => [
                    [
                        'id' => $type->id,
                        'name' => $type->name,
                        'type' => 'type',
                        'sort' => 1,
                        'category_id' => $catB->id,
                    ],
                ],
            ],
        ],
        'uncategorized_types' => [],
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect($type->fresh()->category_id)->toBe($catB->id);
});
