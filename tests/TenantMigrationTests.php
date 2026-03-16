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

use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

describe('2026_03_16_134851_add_citext_unique_to_service_request_type_name', function () {
    it('renames duplicate service request type names with suffix', function () {
        isolatedMigration(
            '2026_03_16_134851_add_citext_unique_to_service_request_type_name',
            function () {
                $type1 = ServiceRequestType::factory()
                    ->state(['name' => 'IT Support'])
                    ->create();

                $type2 = ServiceRequestType::factory()
                    ->state(['name' => 'it support'])
                    ->create();

                $type3 = ServiceRequestType::factory()
                    ->state(['name' => 'IT SUPPORT'])
                    ->create();

                $uniqueType = ServiceRequestType::factory()
                    ->state(['name' => 'HR Support'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_134851_add_citext_unique_to_service_request_type_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $type1->refresh();
                $type2->refresh();
                $type3->refresh();
                $uniqueType->refresh();

                // Oldest record keeps its original name
                expect($type1->name)->toBe('IT Support');

                // Subsequent case-insensitive duplicates get a numeric suffix
                expect($type2->name)->toBe('it support-2');
                expect($type3->name)->toBe('IT SUPPORT-3');

                // Non-duplicate is untouched
                expect($uniqueType->name)->toBe('HR Support');
            }
        );
    });

    it('handles existing suffixed names correctly', function () {
        isolatedMigration(
            '2026_03_16_134851_add_citext_unique_to_service_request_type_name',
            function () {
                $type1 = ServiceRequestType::factory()
                    ->state(['name' => 'IT Support'])
                    ->create();

                $type2 = ServiceRequestType::factory()
                    ->state(['name' => 'IT Support-2'])
                    ->create();

                $type3 = ServiceRequestType::factory()
                    ->state(['name' => 'it support'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_134851_add_citext_unique_to_service_request_type_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $type1->refresh();
                $type2->refresh();
                $type3->refresh();

                expect($type1->name)->toBe('IT Support');

                // Pre-existing suffixed name is untouched
                expect($type2->name)->toBe('IT Support-2');

                // -2 is already taken so the duplicate skips to -3
                expect($type3->name)->toBe('it support-3');
            }
        );
    });

    it('does nothing when no duplicates exist', function () {
        isolatedMigration(
            '2026_03_16_134851_add_citext_unique_to_service_request_type_name',
            function () {
                $type1 = ServiceRequestType::factory()
                    ->state(['name' => 'IT Support'])
                    ->create();

                $type2 = ServiceRequestType::factory()
                    ->state(['name' => 'HR Support'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_134851_add_citext_unique_to_service_request_type_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $type1->refresh();
                $type2->refresh();

                // Both names should be completely unchanged
                expect($type1->name)->toBe('IT Support');
                expect($type2->name)->toBe('HR Support');
            }
        );
    });
});

describe('2026_03_16_162338_add_citext_unique_to_service_request_priority_name', function () {
    it('renames duplicate priority names within the same type with suffix', function () {
        isolatedMigration(
            '2026_03_16_162338_add_citext_unique_to_service_request_priority_name',
            function () {
                $type = ServiceRequestType::factory()->create();

                $priority1 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'High'])
                    ->create();

                $priority2 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'high'])
                    ->create();

                $priority3 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'HIGH'])
                    ->create();

                $uniquePriority = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'Low'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_162338_add_citext_unique_to_service_request_priority_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $priority1->refresh();
                $priority2->refresh();
                $priority3->refresh();
                $uniquePriority->refresh();

                // Oldest record keeps its original name
                expect($priority1->name)->toBe('High');

                // Subsequent case-insensitive duplicates within the same type get a numeric suffix
                expect($priority2->name)->toBe('high-2');
                expect($priority3->name)->toBe('HIGH-3');

                // Non-duplicate is untouched
                expect($uniquePriority->name)->toBe('Low');
            }
        );
    });

    it('does not rename same-named priorities belonging to different types', function () {
        isolatedMigration(
            '2026_03_16_162338_add_citext_unique_to_service_request_priority_name',
            function () {
                $typeA = ServiceRequestType::factory()->create();
                $typeB = ServiceRequestType::factory()->create();

                // Same name but different types — should both survive unchanged
                $priorityA = ServiceRequestPriority::factory()
                    ->for($typeA, 'type')
                    ->state(['name' => 'High'])
                    ->create();

                $priorityB = ServiceRequestPriority::factory()
                    ->for($typeB, 'type')
                    ->state(['name' => 'high'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_162338_add_citext_unique_to_service_request_priority_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $priorityA->refresh();
                $priorityB->refresh();

                // Uniqueness is scoped per type_id
                expect($priorityA->name)->toBe('High');
                expect($priorityB->name)->toBe('high');
            }
        );
    });

    it('handles existing suffixed names correctly within the same type', function () {
        isolatedMigration(
            '2026_03_16_162338_add_citext_unique_to_service_request_priority_name',
            function () {
                $type = ServiceRequestType::factory()->create();

                $priority1 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'Medium'])
                    ->create();

                $priority2 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'Medium-2'])
                    ->create();

                $priority3 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'medium'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_162338_add_citext_unique_to_service_request_priority_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $priority1->refresh();
                $priority2->refresh();
                $priority3->refresh();

                expect($priority1->name)->toBe('Medium');

                // Pre-existing suffixed name is untouched
                expect($priority2->name)->toBe('Medium-2');

                // -2 is already taken so the duplicate skips to -3
                expect($priority3->name)->toBe('medium-3');
            }
        );
    });

    it('does nothing when no duplicates exist, changes column to citext, and creates composite unique index', function () {
        isolatedMigration(
            '2026_03_16_162338_add_citext_unique_to_service_request_priority_name',
            function () {
                $type = ServiceRequestType::factory()->create();

                $priority1 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'High'])
                    ->create();

                $priority2 = ServiceRequestPriority::factory()
                    ->for($type, 'type')
                    ->state(['name' => 'Low'])
                    ->create();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_16_162338_add_citext_unique_to_service_request_priority_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $priority1->refresh();
                $priority2->refresh();

                // Both names should be completely unchanged
                expect($priority1->name)->toBe('High');
                expect($priority2->name)->toBe('Low');

                // Column type should now be citext
                $columnType = DB::selectOne(
                    "SELECT data_type FROM information_schema.columns WHERE table_name = 'service_request_priorities' AND column_name = 'name'"
                );
                expect($columnType->data_type)->toBe('citext');

                // Composite unique index on (name, type_id) should exist
                $indexExists = DB::selectOne(
                    "SELECT 1 FROM pg_indexes WHERE tablename = 'service_request_priorities' AND indexname = 'service_request_priorities_name_type_id_unique'"
                );
                expect($indexExists)->not->toBeNull();
            }
        );
    });
});
