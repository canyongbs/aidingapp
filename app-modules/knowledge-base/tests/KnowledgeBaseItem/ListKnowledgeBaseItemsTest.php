<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Pest\Laravel\get;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

use function Tests\Helpers\testResourceRequiresPermissionForAccess;

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ListKnowledgeBaseItems;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;

// TODO: Write ListKnowledgeBaseItems tests
//test('The correct details are displayed on the ListKnowledgeBaseItems page', function () {});

// TODO: Sorting and Searching tests

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

    $user = User::factory()->licensed(LicenseType::cases())->create();

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

test('ListKnowledgeBaseItems is gated with proper license access control', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');

    // They should not be able to access the resource
    actingAs($user);

    get(
        KnowledgeBaseItemResource::getUrl('index')
    )->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(
        KnowledgeBaseItemResource::getUrl('index')
    )->assertSuccessful();
});

test('Filter ListKnowledgeBaseItems with `quality` filter', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    $goodQuality = KnowledgeBaseQuality::factory()->state([
      'name' => 'Good'
    ])->create();

    $reviewQuality = KnowledgeBaseQuality::factory()->state([
      'name' => 'Review Needed'
    ])->create();

    $badQuality = KnowledgeBaseQuality::factory()->state([
      'name' => 'Bad Quality'
    ])->create();

    // They should not be able to access the resource
    actingAs($user);

    $goodQualityKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($goodQuality,'quality')->create();

    $reviewNeededKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($reviewQuality,'quality')->create();

    $badQualityKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($badQuality,'quality')->create();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($goodQualityKnowledgeBaseItems->merge($reviewNeededKnowledgeBaseItems)->merge($badQualityKnowledgeBaseItems))
        ->filterTable('quality', [$goodQuality,$reviewQuality])
        ->assertCanSeeTableRecords($goodQualityKnowledgeBaseItems->merge($reviewNeededKnowledgeBaseItems))
        ->assertCanNotSeeTableRecords($badQualityKnowledgeBaseItems);

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
      'name' => 'Published'
    ])->create();

    $draft = KnowledgeBaseStatus::factory()->state([
      'name' => 'Draft'
    ])->create();

    $archived = KnowledgeBaseStatus::factory()->state([
      'name' => 'Archived'
    ])->create();

    $publishedKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($published,'status')->create();

    $draftKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($draft,'status')->create();

    $archivedKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($archived,'status')->create();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords($publishedKnowledgeBaseItems->merge($draftKnowledgeBaseItems)->merge($archivedKnowledgeBaseItems))
        ->filterTable('status', [$published,$draft])
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
      'name' => 'Software Installation/Configuration'
    ])->create();

    $passwordManagement = KnowledgeBaseCategory::factory()->state([
      'name' => 'Password Management'
    ])->create();

    $networkTroubleshooting = KnowledgeBaseCategory::factory()->state([
      'name' => 'Network Troubleshooting'
    ])->create();

    $softwareCategoryKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($softwareCategory,'category')->create();

    $passwordManagementKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($passwordManagement,'category')->create();

    $networkTroubleshootingKnowledgeBaseItems = KnowledgeBaseItem::factory()->count(3)->for($networkTroubleshooting,'category')->create();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    livewire(ListKnowledgeBaseItems::class)
        ->assertCanSeeTableRecords(
          $softwareCategoryKnowledgeBaseItems
                                  ->merge($passwordManagementKnowledgeBaseItems)
                                  ->merge($networkTroubleshootingKnowledgeBaseItems)
        )
        ->filterTable('category', [$passwordManagement,$softwareCategory])
        ->assertCanSeeTableRecords(
          $passwordManagementKnowledgeBaseItems
                  ->merge($softwareCategoryKnowledgeBaseItems)
        )
        ->assertCanNotSeeTableRecords($networkTroubleshootingKnowledgeBaseItems);
        
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

    $user->grantLicense(LicenseType::RecruitmentCrm);

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

    $user->grantLicense(LicenseType::RecruitmentCrm);

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

  $user->grantLicense(LicenseType::RecruitmentCrm);

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