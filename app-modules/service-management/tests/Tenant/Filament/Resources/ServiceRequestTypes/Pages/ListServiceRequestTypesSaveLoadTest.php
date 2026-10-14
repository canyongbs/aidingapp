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

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();

    Event::fake([ServiceRequestTypeCatalogUpdated::class]);
});

it('loads hierarchical data', function () {
    $root = ServiceRequestTypeCategory::factory()->create(['name' => 'Root', 'sort' => 1]);
    $child = ServiceRequestTypeCategory::factory()->create(['name' => 'Child', 'sort' => 1, 'parent_id' => $root->id]);

    $type1 = ServiceRequestType::factory()->hasAttached($root, relationship: 'categories')->create(['name' => 'Type 1', 'sort' => 1]);
    $type2 = ServiceRequestType::factory()->hasAttached($child, relationship: 'categories')->create(['name' => 'Type 2', 'sort' => 1]);

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

it('moves a type into an area another user just created', function () {
    $type = ServiceRequestType::factory()->create(['name' => 'Movable', 'sort' => 1]);

    $component = livewire(ListServiceRequestTypes::class);

    $newCategory = ServiceRequestTypeCategory::factory()->create(['name' => 'New Cat']);

    $component->call('moveType', $type->getKey(), null, $newCategory->getKey(), [$type->getKey()]);

    expect($type->fresh()->categoryIds())->toBe([$newCategory->id]);
});

it('moves type from one existing category to another existing category', function () {
    $catA = ServiceRequestTypeCategory::factory()->create(['name' => 'A', 'sort' => 1]);
    $catB = ServiceRequestTypeCategory::factory()->create(['name' => 'B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->hasAttached($catA, relationship: 'categories')->create(['name' => 'ToMove', 'sort' => 1]);

    livewire(ListServiceRequestTypes::class)
        ->call('moveType', $type->getKey(), $catA->getKey(), $catB->getKey(), [$type->getKey()]);

    expect($type->fresh()->categoryIds())->toBe([$catB->id]);
});
