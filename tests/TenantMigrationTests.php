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

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeMultipleCategoriesFeature;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command;

function columnNativeType(string $table, string $column): ?string
{
    return DB::selectOne(
        'SELECT udt_name FROM information_schema.columns WHERE table_name = ? AND column_name = ?',
        [$table, $column],
    )?->udt_name;
}

// Example migration test, leave commented out for future use as a template/example
//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});

describe('2026_07_09_123757_migrate_service_request_types_to_multiple_categories', function () {
    it('backfills the category pivot from the legacy category_id column, activates the feature, and drops the column', function () {
        isolatedMigration(
            '2026_07_09_123757_migrate_service_request_types_to_multiple_categories',
            function () {
                // At this point the pivot table does not yet exist and the legacy `category_id` column is
                // still present, and the feature flag is inactive, so the models write through the legacy column.
                $category = ServiceRequestTypeCategory::factory()->create();

                $typeWithCategory = ServiceRequestType::factory()->create(['category_id' => $category->id]);

                $typeWithoutCategory = ServiceRequestType::factory()->create(['category_id' => null]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_07_09_123757_migrate_service_request_types_to_multiple_categories.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                // The type that had a category is now linked through the pivot table.
                expect(
                    DB::table('service_request_category_types')
                        ->where('service_request_type_id', $typeWithCategory->id)
                        ->where('service_request_type_category_id', $category->id)
                        ->exists()
                )->toBeTrue();

                // The uncategorized type has no pivot row.
                expect(
                    DB::table('service_request_category_types')
                        ->where('service_request_type_id', $typeWithoutCategory->id)
                        ->exists()
                )->toBeFalse();

                // The feature flag is activated as part of the migration.
                expect(ServiceRequestTypeMultipleCategoriesFeature::active())->toBeTrue();

                // The legacy single category column has been dropped.
                expect(columnNativeType('service_request_types', 'category_id'))->toBeNull();
            }
        );
    });
});
