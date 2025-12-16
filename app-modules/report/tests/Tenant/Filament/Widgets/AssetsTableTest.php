<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\InventoryManagement\Models\AssetStatus;
use AidingApp\InventoryManagement\Models\AssetType;
use AidingApp\InventoryManagement\Models\MaintenanceActivity;
use AidingApp\Report\Filament\Widgets\AssetsTable;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('displays all assets with correct information', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $type1 = AssetType::factory()->create(['name' => 'Laptop']);
    $type2 = AssetType::factory()->create(['name' => 'Desktop']);

    $status1 = AssetStatus::factory()->available()->create(['name' => 'Available']);
    $status2 = AssetStatus::factory()->create(['name' => 'Retired']);

    AssetStatus::factory()->state(['name' => 'Under Maintenance'])->create();

    $benchAsset = Asset::factory()
        ->for($type1, 'type')
        ->for($status1, 'status')
        ->state(['name' => 'Bench Laptop 001', 'created_at' => now()->subDays(5)])
        ->create();

    $checkedOutAsset = Asset::factory()
        ->for($type1, 'type')
        ->for($status1, 'status')
        ->has(
            AssetCheckOut::factory()
                ->for(Contact::factory(), 'checkedOutTo')
                ->state(['asset_check_in_id' => null]),
            'checkOuts'
        )
        ->state(['name' => 'Checked Out Laptop 002', 'created_at' => now()->subDays(3)])
        ->create();

    $maintenanceAsset = Asset::factory()
        ->for($type2, 'type')
        ->for($status1, 'status')
        ->state(['name' => 'Maintenance Desktop 001', 'created_at' => now()->subDays(7)])
        ->create();

    MaintenanceActivity::factory()
        ->for($maintenanceAsset, 'asset')
        ->state(['status' => MaintenanceActivityStatus::InProgress])
        ->create();

    $retiredAsset = Asset::factory()
        ->for($type2, 'type')
        ->for($status2, 'status')
        ->state(['name' => 'Retired Desktop 002', 'created_at' => now()->subDays(10)])
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table',
    ])
        ->assertCanSeeTableRecords(collect([
            $benchAsset,
            $checkedOutAsset,
            $maintenanceAsset,
            $retiredAsset,
        ]));
});

it('displays correct deployment status for assets', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());
    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();
    AssetStatus::factory()->create(['name' => 'Under Maintenance']);

    $maintenanceAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            MaintenanceActivity::factory()
                ->state(['status' => MaintenanceActivityStatus::InProgress]),
            'maintenanceActivities'
        )
        ->state(['name' => 'Maintenance Asset'])
        ->create();

    $checkedOutAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            AssetCheckOut::factory()
                ->for(Contact::factory(), 'checkedOutTo')
                ->state(['asset_check_in_id' => null]),
            'checkOuts'
        )
        ->state(['name' => 'Checked Out Asset'])
        ->create();

    $benchAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Bench Stock Asset'])
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-deployment',
    ])
        ->assertCanSeeTableRecords(collect([
            $maintenanceAsset,
            $checkedOutAsset,
            $benchAsset,
        ]))
        ->assertTableColumnStateSet('deployment_status', 'Maintenance', $maintenanceAsset)
        ->assertTableColumnStateSet('deployment_status', 'Checked Out', $checkedOutAsset)
        ->assertTableColumnStateSet('deployment_status', 'Bench Stock', $benchAsset);
});

it('filters assets by name', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();

    $laptop1 = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Laptop001'])
        ->create();

    $laptop2 = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Laptop002'])
        ->create();

    $desktop = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Desktop001'])
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-name-filter',
    ])
        ->filterTable('name', ['Laptop001'])
        ->assertCanSeeTableRecords(collect([$laptop1]))
        ->assertCanNotSeeTableRecords(collect([$laptop2, $desktop]));
});

it('filters assets by type', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $laptopType = AssetType::factory()->create(['name' => 'Laptop']);
    $desktopType = AssetType::factory()->create(['name' => 'Desktop']);
    $status = AssetStatus::factory()->available()->create();

    $laptop = Asset::factory()
        ->for($laptopType, 'type')
        ->for($status, 'status')
        ->create();

    $desktop = Asset::factory()
        ->for($desktopType, 'type')
        ->for($status, 'status')
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-type-filter',
    ])
        ->filterTable('type', [$laptopType->id])
        ->assertCanSeeTableRecords(collect([$laptop]))
        ->assertCanNotSeeTableRecords(collect([$desktop]));
});

it('filters assets by status', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $type = AssetType::factory()->create();
    $availableStatus = AssetStatus::factory()->available()->create(['name' => 'Available']);
    $retiredStatus = AssetStatus::factory()->create(['name' => 'Retired']);

    $availableAsset = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    $retiredAsset = Asset::factory()
        ->for($type, 'type')
        ->for($retiredStatus, 'status')
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-status-filter',
    ])
        ->filterTable('status', [$availableStatus->id])
        ->assertCanSeeTableRecords(collect([$availableAsset]))
        ->assertCanNotSeeTableRecords(collect([$retiredAsset]));
});

it('filters assets by deployment status', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());
    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();
    AssetStatus::factory()->create(['name' => 'Under Maintenance']);

    $maintenanceAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            MaintenanceActivity::factory()
                ->state(['status' => MaintenanceActivityStatus::InProgress]),
            'maintenanceActivities'
        )
        ->create();

    $checkedOutAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            AssetCheckOut::factory()
                ->for(Contact::factory(), 'checkedOutTo')
                ->state(['asset_check_in_id' => null]),
            'checkOuts'
        )
        ->create();

    $benchAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-deployment-filter',
    ])
        ->filterTable('deployment_status', ['Maintenance'])
        ->assertCanSeeTableRecords(collect([$maintenanceAsset]))
        ->assertCanNotSeeTableRecords(collect([$checkedOutAsset, $benchAsset]));
});

it('shows assets ordered by created date descending', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();

    $assetOldest = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Asset Oldest', 'created_at' => now()->subDays(3)])
        ->create();

    $assetMiddle = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Asset Middle', 'created_at' => now()->subDays(2)])
        ->create();

    $assetNewest = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->state(['name' => 'Asset Newest', 'created_at' => now()->subDay()])
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-ordering',
    ])
        ->assertCanSeeTableRecords(collect([$assetNewest, $assetMiddle, $assetOldest]), inOrder: true);
});

it('handles multiple deployment status filters simultaneously', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());
    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();
    AssetStatus::factory()->create(['name' => 'Under Maintenance']);

    $maintenanceAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            MaintenanceActivity::factory()
                ->state(['status' => MaintenanceActivityStatus::InProgress]),
            'maintenanceActivities'
        )
        ->create();

    $checkedOutAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            AssetCheckOut::factory()
                ->for(Contact::factory(), 'checkedOutTo')
                ->state(['asset_check_in_id' => null]),
            'checkOuts'
        )
        ->create();

    $benchAsset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-multiple-deployment-filter',
    ])
        ->filterTable('deployment_status', ['Maintenance', 'Checked Out'])
        ->assertCanSeeTableRecords(collect([$maintenanceAsset, $checkedOutAsset]))
        ->assertCanNotSeeTableRecords(collect([$benchAsset]));
});

it('handles completed maintenance activities correctly for bench stock', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());
    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();
    $asset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            MaintenanceActivity::factory()
                ->state(['status' => MaintenanceActivityStatus::Completed]),
            'maintenanceActivities'
        )
        ->create();

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-completed-maintenance',
    ])
        ->assertCanSeeTableRecords(collect([$asset]))
        ->assertTableColumnStateSet('deployment_status', 'Bench Stock', $asset);
});

it('handles checked-in assets correctly for bench stock', function () {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());
    $type = AssetType::factory()->create();
    $status = AssetStatus::factory()->available()->create();
    $asset = Asset::factory()
        ->for($type, 'type')
        ->for($status, 'status')
        ->has(
            AssetCheckOut::factory()
                ->for(Contact::factory(), 'checkedOutTo'),
            'checkOuts'
        )
        ->create();

    $checkOut = $asset->checkOuts()->first();
    $checkIn = AssetCheckIn::factory()
        ->for($asset, 'asset')
        ->for(Contact::factory(), 'checkedInBy')
        ->create();
    $checkOut->update(['asset_check_in_id' => $checkIn->id]);

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-checked-in',
    ])
        ->assertCanSeeTableRecords(collect([$asset]))
        ->assertTableColumnStateSet('deployment_status', 'Bench Stock', $asset);
});

it('has table an export action', function () {
    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-export',
    ])->assertTableActionExists(ExportAction::class);
});

it('can start an export and send a notification', function () {
    Storage::fake('s3');

    actingAs(User::factory()->create());

    livewire(AssetsTable::class, [
        'cacheTag' => 'test-assets-table-export-notification',
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});
