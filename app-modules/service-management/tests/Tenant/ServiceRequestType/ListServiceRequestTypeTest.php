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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// Permission Tests

test('ListServiceRequestTypes is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('index')
        )->assertSuccessful();
});

test('ListServiceRequestTypes is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl()
        )->assertSuccessful();
});

it('loads hierarchical data', function () {
    asSuperAdmin();

    $root = ServiceRequestTypeCategory::factory()->create(['name' => 'Root', 'sort' => 1]);
    $child = ServiceRequestTypeCategory::factory()->create(['name' => 'Child', 'sort' => 1, 'parent_id' => $root->id]);

    $type1 = ServiceRequestType::factory()->create(['name' => 'Type 1', 'category_id' => $root->id, 'sort' => 1]);
    $type2 = ServiceRequestType::factory()->create(['name' => 'Type 2', 'category_id' => $child->id, 'sort' => 1]);

    $component = livewire(ListServiceRequestTypes::class);

    $component->assertSuccessful();

    // Use the actual component instance to call the method and get the array result
    $data = $component->instance()->getHierarchicalData();

    expect($data['categories'])->toBeArray();
    expect(count($data['categories']))->toBe(1);

    $first = $data['categories'][0];

    expect($first['id'])->toBe($root->id);
    expect(count($first['types']))->toBe(1);
    expect($first['children'][0]['id'])->toBe($child->id);
});

it('creates new category with a new type in one operation', function () {
    asSuperAdmin();

    $tempCategory = 'temp_newcat';
    $tempType = 'temp_newtype';

    $treeData = [
        'categories' => [
            [
                'id' => $tempCategory,
                'name' => 'Brand New',
                'type' => 'category',
                'sort' => 1,
                'parent_id' => null,
                'children' => [],
                'types' => [
                    [
                        'id' => $tempType,
                        'name' => 'Brand New Type',
                        'type' => 'type',
                        'sort' => 1,
                        'category_id' => $tempCategory,
                    ],
                ],
            ],
        ],
        'uncategorized_types' => [],
        'new_categories' => [
            [
                'temp_id' => $tempCategory,
                'name' => 'Brand New',
                'parent_id' => null,
                'sort' => 1,
            ],
        ],
        'new_types' => [
            [
                'temp_id' => $tempType,
                'name' => 'Brand New Type',
                'category_id' => $tempCategory,
                'sort' => 1,
            ],
        ],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    // Assert the category and type exist and are linked
    $category = ServiceRequestTypeCategory::where('name', 'Brand New')->first();
    expect($category)->not->toBeNull();

    $type = ServiceRequestType::where('name', 'Brand New Type')->first();
    expect($type)->not->toBeNull();
    expect($type->category_id)->toBe($category->id);

    // New types should have 3 priorities created
    expect($type->priorities()->count())->toBe(3);
});

it('deletes a type without service requests', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $treeData = [
        'categories' => [],
        'uncategorized_types' => [],
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [$type->id],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect(ServiceRequestType::find($type->id))->toBeNull();
});

it('prevents deleting a type when service requests exist', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);

    $serviceRequest = ServiceRequest::factory()->create(['priority_id' => $priority->id]);

    $treeData = [
        'categories' => [],
        'uncategorized_types' => [],
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [],
        'deleted_types' => [$type->id],
    ];

    // Livewire surfaces validation errors instead of throwing; after save, the type should still exist
    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData);

    expect(ServiceRequestType::find($type->id))->not->toBeNull();
});

it('prevents deleting a category when descendant service requests exist', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Parent Cat']);
    $type = ServiceRequestType::factory()->create(['category_id' => $category->id]);
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);
    $serviceRequest = ServiceRequest::factory()->create(['priority_id' => $priority->id]);

    $treeData = [
        'categories' => [],
        'uncategorized_types' => [],
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [],
        'updated_types' => [],
        'deleted_categories' => [$category->id],
        'deleted_types' => [],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData);

    // The category should still exist because deletion is prevented
    expect(ServiceRequestTypeCategory::find($category->id))->not->toBeNull();
});

it('reorders types within a category', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Reorder Cat']);

    $type1 = ServiceRequestType::factory()->create(['name' => 'One', 'category_id' => $category->id, 'sort' => 1]);
    $type2 = ServiceRequestType::factory()->create(['name' => 'Two', 'category_id' => $category->id, 'sort' => 2]);
    $type3 = ServiceRequestType::factory()->create(['name' => 'Three', 'category_id' => $category->id, 'sort' => 3]);

    // New order: Three, One, Two
    $treeData = [
        'categories' => [
            [
                'id' => $category->id,
                'name' => $category->name,
                'type' => 'category',
                'sort' => 1,
                'parent_id' => null,
                'children' => [],
                'types' => [
                    ['id' => $type3->id, 'name' => $type3->name, 'type' => 'type', 'sort' => 1, 'category_id' => $category->id],
                    ['id' => $type1->id, 'name' => $type1->name, 'type' => 'type', 'sort' => 2, 'category_id' => $category->id],
                    ['id' => $type2->id, 'name' => $type2->name, 'type' => 'type', 'sort' => 3, 'category_id' => $category->id],
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

    expect($type3->fresh()->sort)->toBe(1);
    expect($type1->fresh()->sort)->toBe(2);
    expect($type2->fresh()->sort)->toBe(3);
});

it('updates names for categories and types', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Old Cat']);
    $type = ServiceRequestType::factory()->create(['name' => 'Old Type', 'category_id' => $category->id]);

    $treeData = [
        'categories' => [
            [
                'id' => $category->id,
                'name' => 'Old Cat',
                'type' => 'category',
                'sort' => 1,
                'parent_id' => null,
                'children' => [],
                'types' => [
                    ['id' => $type->id, 'name' => 'New Type Name', 'type' => 'type', 'sort' => 1, 'category_id' => $category->id],
                ],
            ],
        ],
        'uncategorized_types' => [],
        'new_categories' => [],
        'new_types' => [],
        'updated_categories' => [
            ['id' => $category->id, 'name' => 'New Cat Name'],
        ],
        'updated_types' => [
            ['id' => $type->id, 'name' => 'New Type Name'],
        ],
        'deleted_categories' => [],
        'deleted_types' => [],
    ];

    livewire(ListServiceRequestTypes::class)
        ->call('saveChanges', $treeData)
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('New Cat Name');
    expect($type->fresh()->name)->toBe('New Type Name');
});
