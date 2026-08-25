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
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Theme\Settings\ThemeSettings;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

describe('2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique', function () {
    $migrationPath = 'app-modules/contact/database/migrations/2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique.php';

    it('rewrites case-insensitive duplicate emails with plus-addressing and keeps the oldest', function () use ($migrationPath) {
        isolatedMigration(
            '2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $first = Contact::factory()->create(['email' => 'Match@Example.com', 'created_at' => now()->subMinutes(3)]);
                $second = Contact::factory()->create(['email' => 'match@example.com', 'created_at' => now()->subMinutes(2)]);
                $third = Contact::factory()->create(['email' => 'MATCH@EXAMPLE.COM', 'created_at' => now()->subMinutes(1)]);
                $unique = Contact::factory()->create(['email' => 'solo@example.com', 'created_at' => now()->subMinutes(4)]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($first->refresh()->email)->toBe('Match@Example.com')
                    ->and($second->refresh()->email)->toBe('match+2@example.com')
                    ->and($third->refresh()->email)->toBe('MATCH+3@EXAMPLE.COM')
                    ->and($unique->refresh()->email)->toBe('solo@example.com');
            }
        );
    });

    it('keeps the managed contact and rewrites the unmanaged duplicate', function () use ($migrationPath) {
        isolatedMigration(
            '2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $user = User::factory()->create();

                $older = Contact::factory()->create(['email' => 'Shared@Example.com', 'created_at' => now()->subMinutes(5)]);
                $managed = Contact::factory()->create(['email' => 'shared@example.com', 'user_id' => $user->getKey(), 'created_at' => now()->subMinute()]);

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($managed->refresh()->email)->toBe('shared@example.com')
                    ->and($older->refresh()->email)->toBe('Shared+2@Example.com');
            }
        );
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationPath) {
        isolatedMigration(
            '2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique',
            function () use ($migrationPath) {
                $kept = Contact::factory()->create(['email' => 'dupe@example.com', 'created_at' => now()->subMinutes(2)]);
                $trashed = Contact::factory()->create(['email' => 'Dupe@Example.com', 'created_at' => now()->subMinute()]);
                $trashed->delete();

                $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($kept->refresh()->email)->toBe('dupe@example.com')
                    ->and($trashed->refresh()->email)->toBe('Dupe@Example.com');
            }
        );
    });
});

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

describe('2026_08_18_130641_tmp_seed_profile_menu_and_login_home_theme_settings_for_existing_tenants', function () {
    $migrationName = '2026_08_18_130641_tmp_seed_profile_menu_and_login_home_theme_settings_for_existing_tenants';
    $migrationPath = "database/migrations/{$migrationName}.php";

    it('seeds the default profile menu and login and home target settings for existing tenants', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $settings = app(ThemeSettings::class);
            $settings->refresh();

            expect($settings->is_support_url_enabled)->toBeFalse()
                ->and($settings->support_url)->toBeNull()
                ->and($settings->is_recent_updates_url_enabled)->toBeFalse()
                ->and($settings->recent_updates_url)->toBeNull()
                ->and($settings->changelog_url)->toBeNull()
                ->and($settings->product_resource_hub_url)->toBeNull();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $settings->refresh();

            expect($settings->is_support_url_enabled)->toBeTrue()
                ->and($settings->support_url)->toBe(ThemeSettings::DEFAULT_SUPPORT_URL)
                ->and($settings->is_recent_updates_url_enabled)->toBeTrue()
                ->and($settings->recent_updates_url)->toBe(ThemeSettings::DEFAULT_RECENT_UPDATES_URL)
                ->and($settings->changelog_url)->toBe(ThemeSettings::DEFAULT_CHANGELOG_URL)
                ->and($settings->product_resource_hub_url)->toBe(ThemeSettings::DEFAULT_PRODUCT_RESOURCE_HUB_URL);
        });
    });

    it('preserves urls an admin has already configured', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $settings = app(ThemeSettings::class);
            $settings->refresh();
            $settings->support_url = 'https://example.com/custom-support';
            $settings->recent_updates_url = 'https://example.com/custom-updates';
            $settings->changelog_url = 'https://example.com/custom-changelog';
            $settings->product_resource_hub_url = 'https://example.com/custom-hub';
            $settings->save();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $settings->refresh();

            expect($settings->support_url)->toBe('https://example.com/custom-support')
                ->and($settings->recent_updates_url)->toBe('https://example.com/custom-updates')
                ->and($settings->changelog_url)->toBe('https://example.com/custom-changelog')
                ->and($settings->product_resource_hub_url)->toBe('https://example.com/custom-hub')
                ->and($settings->is_support_url_enabled)->toBeTrue()
                ->and($settings->is_recent_updates_url_enabled)->toBeTrue();
        });
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
