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
