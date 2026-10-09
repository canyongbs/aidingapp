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

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ListKnowledgeBaseItems;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use App\Models\Tag;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;
use function Tests\Helpers\testResourceRequiresPermissionForAccess;

// TODO: Write ListKnowledgeBaseItems tests
//test('The correct details are displayed on the ListKnowledgeBaseItems page', function () {});

// TODO: Sorting tests

// Permission Tests

testResourceRequiresPermissionForAccess(
    resource: KnowledgeBaseItemResource::class,
    permissions: 'knowledge_base_item.view-any',
    method: 'index'
);

test('ListKnowledgeBaseItems is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user);

    get(
        KnowledgeBaseItemResource::getUrl('index')
    )->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    get(
        KnowledgeBaseItemResource::getUrl('index')
    )->assertSuccessful();
});

test('Filter ListKnowledgeBaseItems with `status` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    // They should not be able to access the resource
    actingAs($user);

    $published = KnowledgeBaseStatus::factory()->state([
        'name' => 'Published',
    ])->create();

    $draft = KnowledgeBaseStatus::factory()->state([
        'name' => 'Draft',
    ])->create();

    $archived = KnowledgeBaseStatus::factory()->state([
        'name' => 'Archived',
    ])->create();

    $publishedKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($published, 'status')->create();

    $draftKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($draft, 'status')->create();

    $archivedKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($archived, 'status')->create();

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($publishedKnowledgeBaseItems->merge($draftKnowledgeBaseItems)->merge($archivedKnowledgeBaseItems))
        ->filterTable('status', [$published, $draft])
        ->assertCanSeeTableRecords($publishedKnowledgeBaseItems->merge($draftKnowledgeBaseItems))
        ->assertCanNotSeeTableRecords($archivedKnowledgeBaseItems);
});

test('Filter ListKnowledgeBaseItems with `category` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    // They should not be able to access the resource
    actingAs($user);

    $softwareCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Software Installation/Configuration',
    ])->create();

    $passwordManagement = KnowledgeBaseCategory::factory()->state([
        'name' => 'Password Management',
    ])->create();

    $networkTroubleshooting = KnowledgeBaseCategory::factory()->state([
        'name' => 'Network Troubleshooting',
    ])->create();

    $softwareCategoryKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($softwareCategory, 'category')->create();

    $passwordManagementKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($passwordManagement, 'category')->create();

    $networkTroubleshootingKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($networkTroubleshooting, 'category')->create();

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords(
            $softwareCategoryKnowledgeBaseItems
                ->merge($passwordManagementKnowledgeBaseItems)
                ->merge($networkTroubleshootingKnowledgeBaseItems)
        )
        ->filterTable('category', ['categories' => [$passwordManagement->getKey(), $softwareCategory->getKey()]])
        ->assertCanSeeTableRecords(
            $passwordManagementKnowledgeBaseItems
                ->merge($softwareCategoryKnowledgeBaseItems)
        )
        ->assertCanNotSeeTableRecords($networkTroubleshootingKnowledgeBaseItems);
});

test('Filter ListKnowledgeBaseItems with `category` filter only matches the selected category, not its subcategories', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user);

    $parentCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Hardware',
    ])->create();

    $childCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Printers',
        'parent_id' => $parentCategory->getKey(),
    ])->create();

    $parentCategoryKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(2)->for($parentCategory, 'category')->create();

    $childCategoryKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(2)->for($childCategory, 'category')->create();

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($parentCategoryKnowledgeBaseItems->merge($childCategoryKnowledgeBaseItems))
        ->filterTable('category', ['categories' => [$parentCategory->getKey()]])
        ->assertCanSeeTableRecords($parentCategoryKnowledgeBaseItems)
        ->assertCanNotSeeTableRecords($childCategoryKnowledgeBaseItems);
});

it('nests child categories beneath their parent in sort order in the category filter tree', function () {
    $parentCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Hardware',
    ])->create();

    // `sort` and alphabetical name order disagree here: ordering by name would put
    // Printers before Scanners, so asserting Scanners first proves the tree uses `sort`.
    $firstChild = KnowledgeBaseCategory::factory()->state([
        'name' => 'Scanners',
        'parent_id' => $parentCategory->getKey(),
        'sort' => 1,
    ])->create();

    $secondChild = KnowledgeBaseCategory::factory()->state([
        'name' => 'Printers',
        'parent_id' => $parentCategory->getKey(),
        'sort' => 2,
    ])->create();

    $tree = ListKnowledgeBaseItems::buildCategoryTreeOptions();

    expect($tree)
        ->toHaveCount(1)
        ->and($tree[0]['value'])->toBe($parentCategory->getKey())
        ->and($tree[0]['name'])->toBe('Hardware')
        ->and($tree[0]['children'])->toHaveCount(2)
        ->and($tree[0]['children'][0]['value'])->toBe($firstChild->getKey())
        ->and($tree[0]['children'][0]['name'])->toBe('Scanners')
        ->and($tree[0]['children'][0]['children'])->toBe([])
        ->and($tree[0]['children'][1]['value'])->toBe($secondChild->getKey())
        ->and($tree[0]['children'][1]['name'])->toBe('Printers')
        ->and($tree[0]['children'][1]['children'])->toBe([]);
});

it('promotes categories whose parent is soft-deleted to the root of the category filter tree', function () {
    // An existing active root category. Its integer key can collide with the orphan
    // group's key under PHP array union, which is how the package's query() path
    // silently drops a promoted orphan. Asserting both appear guards against that
    // and against anyone switching back to the package's query() method.
    $activeRootCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Software',
        'sort' => 1,
    ])->create();

    $parentCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Hardware',
    ])->create();

    $childCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Printers',
        'parent_id' => $parentCategory->getKey(),
        'sort' => 2,
    ])->create();

    // Soft-deleting the parent leaves the still-active child pointing at a parent
    // that is excluded from the default query, which previously stranded it.
    $parentCategory->delete();

    $tree = ListKnowledgeBaseItems::buildCategoryTreeOptions();

    expect($tree)
        ->toHaveCount(2)
        ->and($tree[0]['value'])->toBe($activeRootCategory->getKey())
        ->and($tree[0]['name'])->toBe('Software')
        ->and($tree[0]['children'])->toBe([])
        ->and($tree[1]['value'])->toBe($childCategory->getKey())
        ->and($tree[1]['name'])->toBe('Printers')
        ->and($tree[1]['children'])->toBe([]);
});

test('Filter ListKnowledgeBaseItems with `public` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    // They should not be able to access the resource
    actingAs($user);

    $knowledgeBaseItems = KnowledgeBaseItem::factory(10)->create();

    $isPublic = true;

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($knowledgeBaseItems)
        ->filterTable('public', $isPublic)
        ->assertCanSeeTableRecords($knowledgeBaseItems->where('public', $isPublic))
        ->assertCanNotSeeTableRecords($knowledgeBaseItems->where('public', '!=', $isPublic));
});

test('Filter ListKnowledgeBaseItems with `created after` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    // They should not be able to access the resource
    actingAs($user);

    $user->refresh();

    $knowledgeBaseItemsCreatedBefore = KnowledgeBaseItem::factory()
        ->count(3)
        ->state(['created_at' => now()->subDays(2)])
        ->create();

    $knowledgeBaseItemsCreatedAfter = KnowledgeBaseItem::factory()
        ->count(3)
        ->state(['created_at' => now()->addDays(2)])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($knowledgeBaseItemsCreatedBefore->merge($knowledgeBaseItemsCreatedAfter))
        ->filterTable('created_at', [
            'created_after' => now(),
        ])
        ->assertCanSeeTableRecords(
            $knowledgeBaseItemsCreatedAfter
        )
        ->assertCanNotSeeTableRecords($knowledgeBaseItemsCreatedBefore);
});

test('Filter ListKnowledgeBaseItems with `updated after` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    // They should not be able to access the resource
    actingAs($user);

    $user->refresh();

    $knowledgeBaseItemsUpdatedAtBefore = KnowledgeBaseItem::factory()
        ->count(3)
        ->state(['updated_at' => now()->subDays(2)])
        ->create();

    $knowledgeBaseItemsUpdatedAtAfter = KnowledgeBaseItem::factory()
        ->count(3)
        ->state(['updated_at' => now()->addDays(2)])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($knowledgeBaseItemsUpdatedAtBefore->merge($knowledgeBaseItemsUpdatedAtAfter))
        ->filterTable('updated_at', [
            'updated_after' => now(),
        ])
        ->assertCanSeeTableRecords(
            $knowledgeBaseItemsUpdatedAtAfter
        )
        ->assertCanNotSeeTableRecords($knowledgeBaseItemsUpdatedAtBefore);
});

test('SearchKnowledgeBaseItems by title', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user);

    $matchingItem = KnowledgeBaseItem::factory()
        ->state(['title' => 'How to reset your password'])
        ->create();

    $nonMatchingItem = KnowledgeBaseItem::factory()
        ->state(['title' => 'Getting started with the app'])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->searchTable('reset your password')
        ->assertCanSeeTableRecords(collect([$matchingItem]))
        ->assertCanNotSeeTableRecords(collect([$nonMatchingItem]));
});

test('SearchKnowledgeBaseItems by article body text', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user);

    $matchingItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'General Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'This article contains unique search term xylophone instructions'],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $nonMatchingItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Another Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'This article is about something completely different'],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->searchTable('xylophone')
        ->assertCanSeeTableRecords(collect([$matchingItem]))
        ->assertCanNotSeeTableRecords(collect([$nonMatchingItem]));
});

test('Health column shows true when knowledge base item has title, article content, manager, and no unresolved concerns', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
            'are_broken_links_detected' => false,
            'are_broken_images_detected' => false,
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', true, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item has empty title', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => '',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item has no article content', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => null,
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item has empty doc article content', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => []],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item article content has only empty text nodes', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '']]],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item has no manager assigned', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
        ])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when knowledge base item has unresolved concern with New status', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    KnowledgeBaseItemConcern::factory()
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->state(['status' => ConcernStatus::New])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows true when knowledge base item has only resolved concerns', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    KnowledgeBaseItemConcern::factory()
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->state(['status' => ConcernStatus::Resolved])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', true, $knowledgeBaseItem);
});

test('Health column shows true when knowledge base item has only archived concerns', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    KnowledgeBaseItemConcern::factory()
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->state(['status' => ConcernStatus::Archived])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', true, $knowledgeBaseItem);
});

test('Health column shows false when multiple conditions fail simultaneously', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => '',
            'article_details' => null,
        ])
        ->create();

    KnowledgeBaseItemConcern::factory()
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->state(['status' => ConcernStatus::New])
        ->create();

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when broken links are detected', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
            'are_broken_links_detected' => true,
            'are_broken_images_detected' => false,
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('Health column shows false when broken images are detected', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'title' => 'Test Article',
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some content']]],
                ],
            ],
            'are_broken_links_detected' => false,
            'are_broken_images_detected' => true,
        ])
        ->create();

    $knowledgeBaseItem->managers()->attach($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableColumnStateSet('health', false, $knowledgeBaseItem);
});

test('an authorised user can duplicate a knowledge base article', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();
    $tags = Tag::factory()->count(3)->forClass(new KnowledgeBaseItem())->create();
    $knowledgeBaseItem->tags()->attach($tags);

    $knowledgeBaseItem->load(['tags']);

    $user = User::factory()->create();
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    actingAs($user);

    livewire(ListKnowledgeBaseItems::class)
        ->callTableAction('replicate', $knowledgeBaseItem, data: [
            'title' => $knowledgeBaseItem->title,
            'public' => $knowledgeBaseItem->public,
            'notes' => $knowledgeBaseItem->notes,
            'status_id' => $knowledgeBaseItem->status_id,
            'category_id' => $knowledgeBaseItem->category_id,
            'tags' => $knowledgeBaseItem->tags->pluck('id')->toArray(),
        ])
        ->assertHasNoTableActionErrors();

    $replicatedKnowledgeBaseItem = KnowledgeBaseItem::query()
        ->whereKeyNot($knowledgeBaseItem->getKey())
        ->sole();

    expect(KnowledgeBaseItem::count())->toBe(2);
    expect($replicatedKnowledgeBaseItem->public_id)->not->toBe($knowledgeBaseItem->public_id);
    expect($replicatedKnowledgeBaseItem->title)->toBe($knowledgeBaseItem->title);
    expect($replicatedKnowledgeBaseItem->public)->toBe($knowledgeBaseItem->public);
    expect($replicatedKnowledgeBaseItem->notes)->toBe($knowledgeBaseItem->notes);
    expect($replicatedKnowledgeBaseItem->status_id)->toBe($knowledgeBaseItem->status_id);
    expect($replicatedKnowledgeBaseItem->category_id)->toBe($knowledgeBaseItem->category_id);
    expect($replicatedKnowledgeBaseItem->tags->pluck('id'))->toEqual($knowledgeBaseItem->tags->pluck('id'));
});

test('duplicating a knowledge base article is gated by the knowledge_base_item.create ability', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $user = User::factory()->create();
    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertTableActionHidden('replicate', $knowledgeBaseItem);
});

it('only shows the bulk delete action to a user with the knowledge_base_item.delete permission', function () {
    KnowledgeBaseItem::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('knowledge_base_item.view-any', 'knowledge_base_item.*.view');

    actingAs($user);

    livewire(ListKnowledgeBaseItems::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('knowledge_base_item.*.delete');

    livewire(ListKnowledgeBaseItems::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});
