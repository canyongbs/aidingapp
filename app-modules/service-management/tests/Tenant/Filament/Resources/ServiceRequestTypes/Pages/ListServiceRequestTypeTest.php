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
use AidingApp\ServiceManagement\Events\ServiceRequestTypeCatalogUpdated;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Tests\asSuperAdmin;

$builderEditor = function (): User {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any', 'settings.*.update');

    return $user;
};

beforeEach(function () {
    // Keep tests from broadcasting to the real websocket service.
    Event::fake([ServiceRequestTypeCatalogUpdated::class]);
});

// Permission Tests

test('ListServiceRequestTypes is gated with proper access control', function () {
    $user = User::factory()->create();

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

    $user = User::factory()->create();

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

    $type1 = ServiceRequestType::factory()->hasAttached($root, relationship: 'categories')->create(['name' => 'Type 1', 'sort' => 1]);
    $type2 = ServiceRequestType::factory()->hasAttached($child, relationship: 'categories')->create(['name' => 'Type 2', 'sort' => 1]);

    $component = Livewire::test(ListServiceRequestTypes::class);

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

it('creates an area and tells other viewers', function () {
    asSuperAdmin();

    assertDatabaseMissing(ServiceRequestTypeCategory::class, ['name' => 'Brand New']);

    $component = Livewire::test(ListServiceRequestTypes::class)
        ->call('createCategory', '  Brand New  ');

    $category = ServiceRequestTypeCategory::query()->where('name', 'Brand New')->firstOrFail();

    expect($category->parent_id)->toBeNull();

    $component->assertReturned(
        function (array $result) use ($category): bool {
            assert(is_array($result['tree']['categories']));

            return $result['saved'] && collect($result['tree']['categories'])->pluck('id')->contains($category->getKey());
        },
    );

    Event::assertDispatched(
        ServiceRequestTypeCatalogUpdated::class,
        fn (ServiceRequestTypeCatalogUpdated $event): bool => $event->tenantId === Tenant::current()->getKey(),
    );
});

it('creates a child area', function () {
    asSuperAdmin();

    $parent = ServiceRequestTypeCategory::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('createCategory', 'Child', $parent->getKey());

    expect(ServiceRequestTypeCategory::query()->where('name', 'Child')->firstOrFail()->parent_id)->toBe($parent->getKey());
});

it('does not nest areas more than two levels deep', function () {
    asSuperAdmin();

    $root = ServiceRequestTypeCategory::factory()->create();
    $child = ServiceRequestTypeCategory::factory()->create(['parent_id' => $root->getKey()]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('createCategory', 'Grandchild', $child->getKey())
        ->assertReturned(fn (array $result): bool => $result['saved'] === false)
        ->assertNotified('Unable to save changes');

    assertDatabaseMissing(ServiceRequestTypeCategory::class, ['name' => 'Grandchild']);
});

it('reports an area that another user has deleted', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $category->delete();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('createType', 'Orphan', $category->getKey())
        ->assertReturned(fn (array $result): bool => $result['saved'] === false)
        ->assertNotified('Unable to save changes');

    assertDatabaseMissing(ServiceRequestType::class, ['name' => 'Orphan']);
});

it('creates a type in an area with default priorities', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $existing = ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('createType', 'Brand New Type', $category->getKey());

    $type = ServiceRequestType::query()->where('name', 'Brand New Type')->firstOrFail();

    expect($type->categories->pluck('id')->all())->toBe([$category->getKey()])
        ->and($type->categories->first()->pivot->sort)->toBe(2)
        ->and($type->priorities()->orderBy('order')->pluck('name')->all())->toBe(['High', 'Medium', 'Low']);
});

it('does not create a type with a name already in use', function () {
    asSuperAdmin();

    ServiceRequestType::factory()->create(['name' => 'Existing']);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('createType', 'existing')
        ->assertReturned(fn (array $result): bool => $result['saved'] === false)
        ->assertNotified('Unable to save changes');

    expect(ServiceRequestType::query()->where('name', 'existing')->count())->toBe(1);
});

it('restores an archived type into an area', function () {
    asSuperAdmin();

    $oldCategory = ServiceRequestTypeCategory::factory()->create();
    $newCategory = ServiceRequestTypeCategory::factory()->create();

    $type = ServiceRequestType::factory()->hasAttached($oldCategory, ['sort' => 1], 'categories')->create();
    $type->archive();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('restoreArchivedType', $type->getKey(), $newCategory->getKey());

    $type->refresh();

    expect($type->isArchived())->toBeFalse()
        ->and($type->categories->pluck('id')->all())->toBe([$newCategory->getKey()]);
});

it('renames an area', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create(['name' => 'Old Cat']);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('renameCategory', $category->getKey(), 'New Cat Name');

    expect($category->refresh()->name)->toBe('New Cat Name');
});

it('renames a type', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create(['name' => 'Old Type']);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('renameType', $type->getKey(), 'New Type Name');

    expect($type->refresh()->name)->toBe('New Type Name');
});

it('does not rename a type to a name already in use', function () {
    asSuperAdmin();

    ServiceRequestType::factory()->create(['name' => 'Taken']);
    $type = ServiceRequestType::factory()->create(['name' => 'Original']);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('renameType', $type->getKey(), 'Taken')
        ->assertNotified('Unable to save changes');

    expect($type->refresh()->name)->toBe('Original');
});

it('deletes a type without service requests', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('deleteType', $type->getKey());

    assertSoftDeleted($type);
});

it('prevents deleting a type when service requests exist', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);
    ServiceRequest::factory()->create(['priority_id' => $priority->id]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('deleteType', $type->getKey())
        ->assertNotified('Unable to save changes');

    assertNotSoftDeleted($type);
});

it('archives a type with service requests', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);
    ServiceRequest::factory()->create(['priority_id' => $priority->id]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('archiveType', $type->getKey());

    expect($type->refresh()->isArchived())->toBeTrue();
});

it('does not archive a type without service requests', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('archiveType', $type->getKey())
        ->assertNotified('Unable to save changes');

    expect($type->refresh()->isArchived())->toBeFalse();
});

it('deletes an area with its child areas and the types filed only within them', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $child = ServiceRequestTypeCategory::factory()->create(['parent_id' => $category->getKey()]);
    $type = ServiceRequestType::factory()->hasAttached($child, ['sort' => 1], 'categories')->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('deleteCategory', $category->getKey());

    assertSoftDeleted($category);
    assertSoftDeleted($child);
    assertSoftDeleted($type);
});

it('prevents deleting an area when its child areas have service requests', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $child = ServiceRequestTypeCategory::factory()->create(['parent_id' => $category->getKey()]);
    $type = ServiceRequestType::factory()->hasAttached($child, ['sort' => 1], 'categories')->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);
    ServiceRequest::factory()->create(['priority_id' => $priority->id]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('deleteCategory', $category->getKey())
        ->assertNotified('Unable to save changes');

    assertNotSoftDeleted($category);
    assertNotSoftDeleted($child);
});

it('moves an area under another area and orders its new siblings', function () {
    asSuperAdmin();

    $parent = ServiceRequestTypeCategory::factory()->create();
    $sibling = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parent->getKey(), 'sort' => 1]);
    $moved = ServiceRequestTypeCategory::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveCategory', $moved->getKey(), $parent->getKey(), [$moved->getKey(), $sibling->getKey()]);

    expect($moved->refresh()->parent_id)->toBe($parent->getKey())
        ->and($moved->sort)->toBe(1)
        ->and($sibling->refresh()->sort)->toBe(2);
});

it('moves a child area to the top level and orders the top-level areas', function () {
    asSuperAdmin();

    $parent = ServiceRequestTypeCategory::factory()->create(['sort' => 1]);
    $child = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parent->getKey(), 'sort' => 1]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveCategory', $child->getKey(), null, [$child->getKey(), $parent->getKey()]);

    expect($child->refresh()->parent_id)->toBeNull()
        ->and($child->sort)->toBe(1)
        ->and($parent->refresh()->sort)->toBe(2);
});

it('does not move an area with child areas under another area', function () {
    asSuperAdmin();

    $target = ServiceRequestTypeCategory::factory()->create();
    $moved = ServiceRequestTypeCategory::factory()->create();
    ServiceRequestTypeCategory::factory()->create(['parent_id' => $moved->getKey()]);

    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveCategory', $moved->getKey(), $target->getKey(), [$moved->getKey()])
        ->assertNotified('Unable to save changes');

    expect($moved->refresh()->parent_id)->toBeNull();
});

it('reorders types within an area, keeping unmentioned types after the requested order', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();

    $type1 = ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create();
    $type2 = ServiceRequestType::factory()->hasAttached($category, ['sort' => 2], 'categories')->create();
    $type3 = ServiceRequestType::factory()->hasAttached($category, ['sort' => 3], 'categories')->create();

    // Another user added `$type3` after this client last loaded the tree, so it is not in the order.
    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveType', $type2->getKey(), $category->getKey(), $category->getKey(), [$type2->getKey(), $type1->getKey()]);

    expect($category->types()->pluck('service_request_types.id')->all())
        ->toBe([$type2->getKey(), $type1->getKey(), $type3->getKey()]);
});

it('moves a type from one area to another', function () {
    asSuperAdmin();

    $from = ServiceRequestTypeCategory::factory()->create();
    $to = ServiceRequestTypeCategory::factory()->create();
    $type = ServiceRequestType::factory()->hasAttached($from, ['sort' => 1], 'categories')->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveType', $type->getKey(), $from->getKey(), $to->getKey(), [$type->getKey()]);

    expect($type->refresh()->categories->pluck('id')->all())->toBe([$to->getKey()]);
});

it('moves an uncategorized type into an area', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $type = ServiceRequestType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->call('moveType', $type->getKey(), null, $category->getKey(), [$type->getKey()]);

    expect($type->refresh()->categories->pluck('id')->all())->toBe([$category->getKey()]);
});

it('restricts a service request type visibility via the manage visibility action', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();
    $contactType = ContactType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->callAction(
            'manageVisibility',
            data: [
                'is_visibility_restricted' => true,
                'contact_type_ids' => [$contactType->id],
            ],
            arguments: [
                'nodeType' => 'type',
                'nodeId' => $type->id,
            ],
        )
        ->assertHasNoActionErrors();

    $type->refresh();

    expect($type->is_visibility_restricted)->toBeTrue()
        ->and($type->restrictedToContactTypes->pluck('id')->all())->toBe([$contactType->id]);

    Event::assertDispatched(ServiceRequestTypeCatalogUpdated::class);
});

it('restricts a service request area visibility via the manage visibility action', function () {
    asSuperAdmin();

    $category = ServiceRequestTypeCategory::factory()->create();
    $contactType = ContactType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->callAction(
            'manageVisibility',
            data: [
                'is_visibility_restricted' => true,
                'contact_type_ids' => [$contactType->id],
            ],
            arguments: [
                'nodeType' => 'category',
                'nodeId' => $category->id,
            ],
        )
        ->assertHasNoActionErrors();

    $category->refresh();

    expect($category->is_visibility_restricted)->toBeTrue()
        ->and($category->restrictedToContactTypes->pluck('id')->all())->toBe([$contactType->id]);
});

it('requires at least one contact type when restricting visibility', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    Livewire::test(ListServiceRequestTypes::class)
        ->callAction(
            'manageVisibility',
            data: [
                'is_visibility_restricted' => true,
                'contact_type_ids' => [],
            ],
            arguments: [
                'nodeType' => 'type',
                'nodeId' => $type->id,
            ],
        )
        ->assertHasActionErrors(['contact_type_ids']);

    expect($type->fresh()->is_visibility_restricted)->toBeFalse();
});

it('clears a visibility restriction and detaches contact types', function () {
    asSuperAdmin();

    $contactType = ContactType::factory()->create();
    $type = ServiceRequestType::factory()->create(['is_visibility_restricted' => true]);
    $type->restrictedToContactTypes()->attach($contactType);

    Livewire::test(ListServiceRequestTypes::class)
        ->callAction(
            'manageVisibility',
            data: [
                'is_visibility_restricted' => false,
            ],
            arguments: [
                'nodeType' => 'type',
                'nodeId' => $type->id,
            ],
        )
        ->assertHasNoActionErrors();

    $type->refresh();

    expect($type->is_visibility_restricted)->toBeFalse()
        ->and($type->restrictedToContactTypes()->count())->toBe(0);
});

it('shows a type under every category it belongs to in the hierarchical data', function () {
    asSuperAdmin();

    $categoryA = ServiceRequestTypeCategory::factory()->create(['name' => 'Area A', 'sort' => 1]);
    $categoryB = ServiceRequestTypeCategory::factory()->create(['name' => 'Area B', 'sort' => 2]);

    $type = ServiceRequestType::factory()->create(['name' => 'Shared', 'sort' => 1]);
    $type->categories()->attach([$categoryA->id, $categoryB->id]);

    $data = Livewire::test(ListServiceRequestTypes::class)->instance()->getHierarchicalData();
    assert(is_array($data['categories']));

    $firstCategoryData = collect($data['categories'])->firstWhere('id', $categoryA->id);
    $secondCategoryData = collect($data['categories'])->firstWhere('id', $categoryB->id);
    assert(is_array($firstCategoryData['types']));
    assert(is_array($secondCategoryData['types']));

    expect(collect($firstCategoryData['types'])->pluck('id'))->toContain($type->id)
        ->and(collect($secondCategoryData['types'])->pluck('id'))->toContain($type->id);
});

describe('authorization', function () use ($builderEditor) {
    it('denies builder changes without the `settings.*.update` permission', function (string $method, array $arguments) {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ListServiceRequestTypes::class)
            ->call($method, ...$arguments)
            ->assertForbidden();

        Event::assertNotDispatched(ServiceRequestTypeCatalogUpdated::class);
    })->with([
        'createCategory' => ['createCategory', ['Area']],
        'renameCategory' => fn () => ['renameCategory', [(string) Str::uuid(), 'Area']],
        'moveCategory' => fn () => ['moveCategory', [(string) Str::uuid(), null, []]],
        'deleteCategory' => fn () => ['deleteCategory', [(string) Str::uuid()]],
        'createType' => ['createType', ['Type']],
        'restoreArchivedType' => fn () => ['restoreArchivedType', [(string) Str::uuid()]],
        'renameType' => fn () => ['renameType', [(string) Str::uuid(), 'Type']],
        'moveType' => fn () => ['moveType', [(string) Str::uuid(), null, null, []]],
        'addTypeToCategory' => fn () => ['addTypeToCategory', [(string) Str::uuid(), (string) Str::uuid()]],
        'removeTypeFromCategory' => fn () => ['removeTypeFromCategory', [(string) Str::uuid(), (string) Str::uuid()]],
        'deleteType' => fn () => ['deleteType', [(string) Str::uuid()]],
        'archiveType' => fn () => ['archiveType', [(string) Str::uuid()]],
    ]);

    it('does not create a type without the `settings.create` permission', function () use ($builderEditor) {
        actingAs($builderEditor());

        Livewire::test(ListServiceRequestTypes::class)
            ->call('createType', 'Unauthorized Type')
            ->assertReturned(fn (array $result): bool => $result['saved'] === false)
            ->assertNotified('Unable to save changes');

        assertDatabaseMissing(ServiceRequestType::class, ['name' => 'Unauthorized Type']);
    });

    it('does not delete a type without the `settings.*.delete` permission', function () use ($builderEditor) {
        actingAs($builderEditor());

        $type = ServiceRequestType::factory()->create();

        Livewire::test(ListServiceRequestTypes::class)
            ->call('deleteType', $type->getKey())
            ->assertNotified('Unable to save changes');

        assertNotSoftDeleted($type);
    });

    it('does not archive a type without the `settings.*.delete` permission', function () use ($builderEditor) {
        actingAs($builderEditor());

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->id]);
        ServiceRequest::factory()->create(['priority_id' => $priority->id]);

        Livewire::test(ListServiceRequestTypes::class)
            ->call('archiveType', $type->getKey())
            ->assertNotified('Unable to save changes');

        expect($type->refresh()->isArchived())->toBeFalse();
    });

    it('does not restore an archived type without the `settings.*.restore` permission', function () use ($builderEditor) {
        actingAs($builderEditor());

        $type = ServiceRequestType::factory()->create();
        $type->archive();

        Livewire::test(ListServiceRequestTypes::class)
            ->call('restoreArchivedType', $type->getKey())
            ->assertNotified('Unable to save changes');

        expect($type->refresh()->isArchived())->toBeTrue();
    });

    it('does not delete an area whose types would be deleted without the `settings.*.delete` permission', function () use ($builderEditor) {
        actingAs($builderEditor());

        $category = ServiceRequestTypeCategory::factory()->create();
        $type = ServiceRequestType::factory()->hasAttached($category, ['sort' => 1], 'categories')->create();

        Livewire::test(ListServiceRequestTypes::class)
            ->call('deleteCategory', $category->getKey())
            ->assertNotified('Unable to save changes');

        assertNotSoftDeleted($category);
        assertNotSoftDeleted($type);
    });

    it('lets a user who can only update the builder change areas and type placements', function () use ($builderEditor) {
        actingAs($builderEditor());

        $category = ServiceRequestTypeCategory::factory()->create();
        $type = ServiceRequestType::factory()->create();

        Livewire::test(ListServiceRequestTypes::class)
            ->call('createCategory', 'Editor Area')
            ->call('moveType', $type->getKey(), null, $category->getKey(), [$type->getKey()]);

        expect(ServiceRequestTypeCategory::query()->where('name', 'Editor Area')->exists())->toBeTrue()
            ->and($type->refresh()->categories->pluck('id')->all())->toBe([$category->getKey()]);
    });
});
