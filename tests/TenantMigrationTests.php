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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

// TODO: Cleanup Task Service Request Division Decoupling - delete this describe and the test within
describe('2026_09_14_220000_tmp_remove_division_from_service_request_histories', function () {
    it('removes division-only history rows and preserves other changes', function () {
        isolatedMigration('2026_09_14_220000_tmp_remove_division_from_service_request_histories', function () {
            // These fixtures predate later contact and user columns, so avoid factory values
            // outside the migration's schema snapshot.
            $respondentId = (string) Str::uuid();

            DB::table('contacts')->insert([
                'id' => $respondentId,
                ...Arr::only(Contact::factory()->raw(['created_by_id' => null]), Schema::getColumnListing('contacts')),
            ]);

            $serviceRequest = ServiceRequest::factory()->create([
                'created_by_id' => null,
                'respondent_id' => $respondentId,
            ]);

            DB::table('service_request_histories')->delete();

            recordServiceRequestHistory(
                $serviceRequest,
                ['division_id' => 'old-division-id'],
                ['division_id' => 'new-division-id'],
                now(),
            );
            recordServiceRequestHistory(
                $serviceRequest,
                ['division_id' => 'old-division-id', 'title' => 'Old title'],
                ['division_id' => 'new-division-id', 'title' => 'New title'],
                now(),
            );

            $historiesBeforeMigration = DB::table('service_request_histories')
                ->where('service_request_id', $serviceRequest->getKey())
                ->orderBy('created_at')
                ->get();

            DB::table('timelines')->insert([
                [
                    'id' => (string) Str::uuid(),
                    'entity_type' => 'service_request',
                    'entity_id' => $serviceRequest->getKey(),
                    'timelineable_type' => 'service_request_history',
                    'timelineable_id' => $historiesBeforeMigration[0]->id,
                    'record_sortable_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'entity_type' => 'service_request',
                    'entity_id' => $serviceRequest->getKey(),
                    'timelineable_type' => 'service_request_history',
                    'timelineable_id' => $historiesBeforeMigration[1]->id,
                    'record_sortable_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Run the migration
            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_09_14_220000_tmp_remove_division_from_service_request_histories.php']);
            // Confirm migration ran successfully
            expect($migrate)->toBe(Command::SUCCESS);

            // Add any assertions to verify the migration's effects
            $histories = DB::table('service_request_histories')
                ->where('service_request_id', $serviceRequest->getKey())
                ->get();

            expect($histories)->toHaveCount(1)
                ->and(json_decode($histories->first()->original_values, true))->toBe(['title' => 'Old title'])
                ->and(json_decode($histories->first()->new_values, true))->toBe(['title' => 'New title']);

            $timelines = DB::table('timelines')
                ->where('timelineable_type', 'service_request_history')
                ->whereIn('timelineable_id', $historiesBeforeMigration->pluck('id'))
                ->get();

            expect($timelines)->toHaveCount(1)
                ->and($timelines->first()->timelineable_id)->toBe($historiesBeforeMigration[1]->id);
        });
    });
});
