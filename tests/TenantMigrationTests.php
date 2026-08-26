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

use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (! function_exists('recordServiceRequestHistory')) {
    /**
     * @param array<string, mixed> $originalValues
     * @param array<string, mixed> $newValues
     */
    function recordServiceRequestHistory(ServiceRequest $serviceRequest, array $originalValues, array $newValues, CarbonInterface $createdAt): void
    {
        DB::table('service_request_histories')->insert([
            'id' => (string) Str::uuid(),
            'service_request_id' => $serviceRequest->getKey(),
            'original_values' => json_encode($originalValues),
            'new_values' => json_encode($newValues),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}

describe('2026_08_24_000001_convert_organizations_name_to_citext_and_enforce_unique', function () {
    $migrationPath = 'app-modules/contact/database/migrations/2026_08_24_000001_convert_organizations_name_to_citext_and_enforce_unique.php';

    it('rewrites case-insensitive duplicate names with a numeric suffix and keeps the oldest', function () use ($migrationPath) {
        isolatedMigration(
            '2026_08_24_000001_convert_organizations_name_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $first = Organization::factory()->create(['name' => 'Acme Corp', 'created_at' => now()->subMinutes(3)]);
                $second = Organization::factory()->create(['name' => 'acme corp', 'created_at' => now()->subMinutes(2)]);
                $third = Organization::factory()->create(['name' => 'ACME CORP', 'created_at' => now()->subMinutes(1)]);
                $unique = Organization::factory()->create(['name' => 'Solo Inc', 'created_at' => now()->subMinutes(4)]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($first->refresh()->name)->toBe('Acme Corp')
                    ->and($second->refresh()->name)->toBe('acme corp-2')
                    ->and($third->refresh()->name)->toBe('ACME CORP-3')
                    ->and($unique->refresh()->name)->toBe('Solo Inc');
            }
        );
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationPath) {
        isolatedMigration(
            '2026_08_24_000001_convert_organizations_name_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $kept = Organization::factory()->create(['name' => 'Dupe Co', 'created_at' => now()->subMinutes(2)]);
                $trashed = Organization::factory()->create(['name' => 'dupe co', 'created_at' => now()->subMinute()]);
                $trashed->delete();

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($kept->refresh()->name)->toBe('Dupe Co')
                    ->and($trashed->refresh()->name)->toBe('dupe co');
            }
        );
    });
});

describe('2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique', function () {
    $migrationPath = 'app-modules/contact/database/migrations/2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique.php';

    it('rewrites case-insensitive duplicate type names with a numeric suffix and keeps the oldest', function () use ($migrationPath) {
        isolatedMigration(
            '2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $first = OrganizationType::factory()->create(['name' => 'Vendor', 'created_at' => now()->subMinutes(3)]);
                $second = OrganizationType::factory()->create(['name' => 'vendor', 'created_at' => now()->subMinutes(2)]);
                $third = OrganizationType::factory()->create(['name' => 'VENDOR', 'created_at' => now()->subMinutes(1)]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($first->refresh()->name)->toBe('Vendor')
                    ->and($second->refresh()->name)->toBe('vendor-2')
                    ->and($third->refresh()->name)->toBe('VENDOR-3');
            }
        );
    });

    it('rewrites case-insensitive duplicate industry names with a numeric suffix and keeps the oldest', function () use ($migrationPath) {
        isolatedMigration(
            '2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $first = OrganizationIndustry::factory()->create(['name' => 'Technology', 'created_at' => now()->subMinutes(3)]);
                $second = OrganizationIndustry::factory()->create(['name' => 'technology', 'created_at' => now()->subMinutes(2)]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($first->refresh()->name)->toBe('Technology')
                    ->and($second->refresh()->name)->toBe('technology-2');
            }
        );
    });

    it('leaves soft-deleted duplicate type names untouched', function () use ($migrationPath) {
        isolatedMigration(
            '2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $kept = OrganizationType::factory()->create(['name' => 'Partner', 'created_at' => now()->subMinutes(2)]);
                $trashed = OrganizationType::factory()->create(['name' => 'partner', 'created_at' => now()->subMinute()]);
                $trashed->delete();

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($kept->refresh()->name)->toBe('Partner')
                    ->and($trashed->refresh()->name)->toBe('partner');
            }
        );
    });
});

// Example migration test, leave commented out for future use as a template/example
describe('2026_08_12_163559_tmp_backfill_service_request_status_periods', function () {
    $migrationName = '2026_08_12_163559_tmp_backfill_service_request_status_periods';
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    it('backfills a status period ledger from the service request history', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $start = CarbonImmutable::parse('2026-01-01 00:00:00');

            $open = ServiceRequestStatus::factory()->open()->create();
            $waiting = ServiceRequestStatus::factory()->waiting()->create();
            $closed = ServiceRequestStatus::factory()->closed()->create();

            $serviceRequest = ServiceRequest::factory()->create([
                'status_id' => $closed->getKey(),
                'created_at' => $start,
            ]);

            // Start from a clean slate: drop any ledger and history the observers recorded on create.
            DB::table('service_request_status_periods')->delete();
            DB::table('service_request_histories')->delete();

            recordServiceRequestHistory($serviceRequest, [], ['status_id' => $open->getKey()], $start);
            recordServiceRequestHistory($serviceRequest, ['status_id' => $open->getKey()], ['status_id' => $waiting->getKey()], $start->addSeconds(100));
            recordServiceRequestHistory($serviceRequest, ['status_id' => $waiting->getKey()], ['status_id' => $closed->getKey()], $start->addSeconds(200));

            expect(Artisan::call('migrate', ['--path' => $migrationPath]))->toBe(Command::SUCCESS);

            $periods = DB::table('service_request_status_periods')
                ->where('service_request_id', $serviceRequest->getKey())
                ->orderBy('started_at')
                ->get();

            expect($periods)->toHaveCount(3)
                ->and($periods[0]->service_request_status_id)->toBe($open->getKey())
                ->and($periods[0]->classification)->toBe(SystemServiceRequestClassification::Open->value)
                ->and($periods[1]->service_request_status_id)->toBe($waiting->getKey())
                ->and($periods[1]->classification)->toBe(SystemServiceRequestClassification::Waiting->value)
                ->and($periods[2]->service_request_status_id)->toBe($closed->getKey())
                ->and($periods[2]->classification)->toBe(SystemServiceRequestClassification::Closed->value);
        });
    });

    it('records a null classification period when a historical status was hard-deleted', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $start = CarbonImmutable::parse('2026-01-01 00:00:00');

            $open = ServiceRequestStatus::factory()->open()->create();
            $deletedStatusId = (string) Str::uuid();

            $serviceRequest = ServiceRequest::factory()->create([
                'status_id' => $open->getKey(),
                'created_at' => $start,
            ]);

            DB::table('service_request_status_periods')->delete();
            DB::table('service_request_histories')->delete();

            recordServiceRequestHistory($serviceRequest, [], ['status_id' => $open->getKey()], $start);
            recordServiceRequestHistory($serviceRequest, ['status_id' => $open->getKey()], ['status_id' => $deletedStatusId], $start->addSeconds(100));

            expect(Artisan::call('migrate', ['--path' => $migrationPath]))->toBe(Command::SUCCESS);

            $periods = DB::table('service_request_status_periods')
                ->where('service_request_id', $serviceRequest->getKey())
                ->orderBy('started_at')
                ->get();

            expect($periods)->toHaveCount(2)
                ->and($periods[0]->service_request_status_id)->toBe($open->getKey())
                ->and($periods[0]->classification)->toBe(SystemServiceRequestClassification::Open->value)
                ->and($periods[1]->service_request_status_id)->toBeNull()
                ->and($periods[1]->classification)->toBeNull();
        });
    });
});

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
