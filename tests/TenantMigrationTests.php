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

use App\Features\ServiceRequestTypeDirectUserManagersFeature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

describe('2026_03_04_000000_add_service_request_type_direct_user_manager_auditor_tables', function () {
    it('renames team tables, creates user tables, preserves data, and activates feature flag', function () {
        isolatedMigration(
            '2026_03_04_000000_add_service_request_type_direct_user_manager_auditor_tables',
            function () {
                // Verify old tables exist before migration
                expect(Schema::hasTable('service_request_type_managers'))->toBeTrue();
                expect(Schema::hasTable('service_request_type_auditors'))->toBeTrue();

                // Create a service request type to reference
                $typeId = Str::uuid()->toString();
                DB::table('service_request_types')->insert([
                    'id' => $typeId,
                    'name' => 'Test Type',
                    'has_enabled_feedback_collection' => false,
                    'has_enabled_csat' => false,
                    'has_enabled_nps' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create a team to reference
                $teamId = Str::uuid()->toString();
                DB::table('teams')->insert([
                    'id' => $teamId,
                    'name' => 'Test Team',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert manager and auditor records into old tables
                $managerId = Str::uuid()->toString();
                DB::table('service_request_type_managers')->insert([
                    'id' => $managerId,
                    'service_request_type_id' => $typeId,
                    'team_id' => $teamId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $auditorId = Str::uuid()->toString();
                DB::table('service_request_type_auditors')->insert([
                    'id' => $auditorId,
                    'service_request_type_id' => $typeId,
                    'team_id' => $teamId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Run the migration
                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_03_04_000000_add_service_request_type_direct_user_manager_auditor_tables.php',
                ]);
                expect($migrate)->toBe(Command::SUCCESS);

                // Old table names should no longer exist
                expect(Schema::hasTable('service_request_type_managers'))->toBeFalse();
                expect(Schema::hasTable('service_request_type_auditors'))->toBeFalse();

                // New team tables should exist with renamed data
                expect(Schema::hasTable('service_request_type_manager_teams'))->toBeTrue();
                expect(Schema::hasTable('service_request_type_auditor_teams'))->toBeTrue();

                // New user tables should exist
                expect(Schema::hasTable('service_request_type_manager_users'))->toBeTrue();
                expect(Schema::hasTable('service_request_type_auditor_users'))->toBeTrue();

                // Verify data was preserved in renamed tables
                $managerRecord = DB::table('service_request_type_manager_teams')->where('id', $managerId)->first();
                expect($managerRecord)->not->toBeNull();
                expect($managerRecord->service_request_type_id)->toBe($typeId);
                expect($managerRecord->team_id)->toBe($teamId);

                $auditorRecord = DB::table('service_request_type_auditor_teams')->where('id', $auditorId)->first();
                expect($auditorRecord)->not->toBeNull();
                expect($auditorRecord->service_request_type_id)->toBe($typeId);
                expect($auditorRecord->team_id)->toBe($teamId);

                // Verify user tables have correct columns
                expect(Schema::hasColumns('service_request_type_manager_users', ['id', 'service_request_type_id', 'user_id', 'created_at', 'updated_at']))->toBeTrue();
                expect(Schema::hasColumns('service_request_type_auditor_users', ['id', 'service_request_type_id', 'user_id', 'created_at', 'updated_at']))->toBeTrue();

                // Verify the feature flag was activated
                expect(ServiceRequestTypeDirectUserManagersFeature::active())->toBeTrue();
            }
        );
    });
});
