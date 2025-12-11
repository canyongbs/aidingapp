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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AidingApp\Report\Filament\Widgets\AssetStats;

it('returns correct asset statistics with various asset states', function () {
    $availableStatus = AssetStatus::factory()
        ->available()
        ->state(['name' => 'Available'])
        ->create();
    $underMaintenanceStatus = AssetStatus::factory()
        ->unavailable()
        ->state(['name' => 'Under Maintenance'])
        ->create();

    $type = AssetType::factory()->create();
    $contact1 = Contact::factory()->create();
    $contact2 = Contact::factory()->create();

    // 1. Asset with active maintenance (scheduled) - should count as "In Maintenance"
    $assetInMaintenance1 = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    MaintenanceActivity::factory()
        ->for($assetInMaintenance1, 'asset')
        ->state(['status' => MaintenanceActivityStatus::Scheduled])
        ->create();

    // 2. Asset with active maintenance (in progress) - should count as "In Maintenance"
    $assetInMaintenance2 = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    MaintenanceActivity::factory()
        ->for($assetInMaintenance2, 'asset')
        ->state(['status' => MaintenanceActivityStatus::InProgress])
        ->create();

    // 3. Asset with completed maintenance - should NOT count as "In Maintenance"
    $assetMaintenanceCompleted = Asset::factory()
        ->for($type, 'type')
        ->for($underMaintenanceStatus, 'status')
        ->create();

    MaintenanceActivity::factory()
        ->for($assetMaintenanceCompleted, 'asset')
        ->state(['status' => MaintenanceActivityStatus::Completed])
        ->create();

    // 4. Asset checked out (no maintenance) - should count as "Checked Out"
    $assetCheckedOut = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    AssetCheckOut::factory()
        ->for($assetCheckedOut, 'asset')
        ->for($contact1, 'checkedOutTo')
        ->state(['asset_check_in_id' => null])
        ->create();

    // 5. Asset checked out AND under maintenance - should count as "In Maintenance" (maintenance takes priority)
    $assetCheckedOutInMaintenance = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    AssetCheckOut::factory()
        ->for($assetCheckedOutInMaintenance, 'asset')
        ->for($contact2, 'checkedOutTo')
        ->state(['asset_check_in_id' => null])
        ->create();

    MaintenanceActivity::factory()
        ->for($assetCheckedOutInMaintenance, 'asset')
        ->state(['status' => MaintenanceActivityStatus::InProgress])
        ->create();

    // 6. Asset in bench stock - should count as "Bench Stock"
    $assetBenchStock = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    // 7. Asset checked out but returned - should count as "Bench Stock"
    $assetReturned = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    $checkOut = AssetCheckOut::factory()
        ->for($assetReturned, 'asset')
        ->for($contact1, 'checkedOutTo')
        ->create();

    $checkIn = AssetCheckIn::factory()
        ->for($assetReturned, 'asset')
        ->for($contact1, 'checkedInFrom')
        ->state(['checked_in_at' => now()])
        ->create();

    $checkOut->update(['asset_check_in_id' => $checkIn->id]);

    $widget = new AssetStats();
    $widget->cacheTag = 'test-asset-stats';

    $stats = $widget->getStats();

    $expectedTotalAssets = 7; // All assets
    $expectedInMaintenance = 3; // assetInMaintenance1, assetInMaintenance2, assetCheckedOutInMaintenance
    $expectedCheckedOut = 2; // assetCheckedOut, assetCheckedOutInMaintenance (both count as checked out regardless of maintenance)
    $expectedBenchStock = 3; // assetMaintenanceCompleted, assetBenchStock, assetReturned

    expect($stats)->toHaveCount(4)
        ->and($stats[0]->getValue())->toEqual((string) $expectedTotalAssets) // Total Assets
        ->and($stats[1]->getValue())->toEqual((string) $expectedInMaintenance) // In Maintenance
        ->and($stats[2]->getValue())->toEqual((string) $expectedCheckedOut) // Checked Out
        ->and($stats[3]->getValue())->toEqual((string) $expectedBenchStock); // Bench Stock
});

it('returns correct statistics with different maintenance statuses', function () {
    $availableStatus = AssetStatus::factory()
        ->available()
        ->state(['name' => 'Available'])
        ->create();
    $type = AssetType::factory()->create();

    // Asset with delayed maintenance - should count as "In Maintenance"
    $assetDelayedMaintenance = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    MaintenanceActivity::factory()
        ->for($assetDelayedMaintenance, 'asset')
        ->state(['status' => MaintenanceActivityStatus::Delayed])
        ->create();

    // Asset with canceled maintenance - should NOT count as "In Maintenance"
    $assetCanceledMaintenance = Asset::factory()
        ->for($type, 'type')
        ->for($availableStatus, 'status')
        ->create();

    MaintenanceActivity::factory()
        ->for($assetCanceledMaintenance, 'asset')
        ->state(['status' => MaintenanceActivityStatus::Canceled])
        ->create();

    $widget = new AssetStats();
    $widget->cacheTag = 'test-asset-stats-maintenance-statuses';

    $stats = $widget->getStats();

    $expectedTotalAssets = 2;
    $expectedInMaintenance = 1; // Only assetDelayedMaintenance
    $expectedCheckedOut = 0;
    $expectedBenchStock = 1; // assetCanceledMaintenance

    expect($stats)->toHaveCount(4)
        ->and($stats[0]->getValue())->toEqual((string) $expectedTotalAssets) // Total Assets
        ->and($stats[1]->getValue())->toEqual((string) $expectedInMaintenance) // In Maintenance
        ->and($stats[2]->getValue())->toEqual((string) $expectedCheckedOut) // Checked Out
        ->and($stats[3]->getValue())->toEqual((string) $expectedBenchStock); // Bench Stock
});

it('returns zero counts when no assets exist', function () {
    $widget = new AssetStats();
    $widget->cacheTag = 'test-asset-stats-empty';

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(4)
        ->and($stats[0]->getValue())->toEqual('0') // Total Assets
        ->and($stats[1]->getValue())->toEqual('0') // In Maintenance
        ->and($stats[2]->getValue())->toEqual('0') // Checked Out
        ->and($stats[3]->getValue())->toEqual('0'); // Bench Stock
});
