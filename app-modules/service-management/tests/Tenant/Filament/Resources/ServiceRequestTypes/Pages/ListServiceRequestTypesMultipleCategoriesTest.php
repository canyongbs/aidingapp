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

use AidingApp\ServiceManagement\Events\ServiceRequestTypeCatalogUpdated;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();

    Event::fake([ServiceRequestTypeCatalogUpdated::class]);
});

it('files a type under another area', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->hasAttached($catA, ['sort' => 1], 'categories')->create(['name' => 'Shared', 'sort' => 1]);

    livewire(ListServiceRequestTypes::class)
        ->call('addTypeToCategory', $type->getKey(), $catB->getKey());

    expect($type->fresh()->categories->pluck('id')->sort()->values()->all())
        ->toBe(collect([$catA->id, $catB->id])->sort()->values()->all());
});

it('stores a distinct per-area sort on each membership', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $shared = ServiceRequestType::factory()->hasAttached($catA, ['sort' => 1], 'categories')->create(['name' => 'Shared', 'sort' => 1]);
    ServiceRequestType::factory()->hasAttached($catB, ['sort' => 1], 'categories')->create(['name' => 'Other', 'sort' => 1]);

    // In A the shared type stays first; in B it is added after "Other".
    livewire(ListServiceRequestTypes::class)
        ->call('addTypeToCategory', $shared->getKey(), $catB->getKey());

    $shared = $shared->fresh();

    expect($shared->categories->firstWhere('id', $catA->id)->pivot->sort)->toBe(1)
        ->and($shared->categories->firstWhere('id', $catB->id)->pivot->sort)->toBe(2);
});

it('detaches a single membership when a type is removed from one of its areas', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('removeTypeFromCategory', $type->getKey(), $catB->getKey());

    expect($type->fresh()->categories->pluck('id')->all())->toBe([$catA->id]);
});

it('does not remove a type from its only area', function () {
    $category = ServiceRequestTypeCategory::factory()->create();

    $type = ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create();

    livewire(ListServiceRequestTypes::class)
        ->call('removeTypeFromCategory', $type->getKey(), $category->getKey())
        ->assertNotified('Unable to save changes');

    expect($type->fresh()->categories->pluck('id')->all())->toBe([$category->id]);
});

it('uncategorizes a type moved out of its only area', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);

    $type = ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create(['name' => 'Loose', 'sort' => 1]);

    livewire(ListServiceRequestTypes::class)
        ->call('moveType', $type->getKey(), $category->getKey(), null, [$type->getKey()]);

    expect($type->fresh()->categories)->toBeEmpty();
});

it('does not uncategorize a type filed under several areas', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('moveType', $type->getKey(), $catA->getKey(), null, [$type->getKey()])
        ->assertNotified('Unable to save changes');

    expect($type->fresh()->categories)->toHaveCount(2);
});

it('keeps a shared type when one of its areas is deleted', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->sync([
        $catA->id => ['sort' => 1],
        $catB->id => ['sort' => 1],
    ]);

    livewire(ListServiceRequestTypes::class)
        ->call('deleteCategory', $catB->getKey());

    assertNotSoftDeleted($type);
    assertSoftDeleted($catB);

    expect($type->fresh()->categories->pluck('id')->all())->toBe([$catA->id]);
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

it('creates an uncategorized type with no memberships', function () {
    livewire(ListServiceRequestTypes::class)
        ->call('createType', 'Loner');

    $type = ServiceRequestType::query()->where('name', 'Loner')->firstOrFail();

    expect($type->categories)->toBeEmpty();
});

it('stores the per-area sort for a newly created type', function () {
    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Area', 'sort' => 1]);

    ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create(['name' => 'First', 'sort' => 1]);
    ServiceRequestType::factory()->hasAttached($category, ['sort' => 2], 'categories')->create(['name' => 'Second', 'sort' => 2]);

    livewire(ListServiceRequestTypes::class)
        ->call('createType', 'Brand New', $category->getKey());

    $type = ServiceRequestType::query()->where('name', 'Brand New')->firstOrFail();

    expect($type->categories->pluck('id')->all())->toBe([$category->id])
        ->and($type->categories->firstWhere('id', $category->id)->pivot->sort)->toBe(3);
});
