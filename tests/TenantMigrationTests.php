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
use AidingApp\Theme\Settings\ThemeSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
