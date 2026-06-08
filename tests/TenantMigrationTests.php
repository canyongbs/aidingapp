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

use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Authorization\Models\Role;
use AidingApp\Department\Models\Department;
use AidingApp\Division\Models\Division;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

describe('2026_05_15_201835_tmp_update_ai_support_assistant_default_instructions', function () {
    it('overwrites any existing AI support assistant instructions with the new default', function () {
        isolatedMigration(
            '2026_05_15_201835_tmp_update_ai_support_assistant_default_instructions',
            function () {
                DB::table('settings')
                    ->where('group', 'ai-support-assistant')
                    ->where('name', 'instructions')
                    ->update(['payload' => json_encode('Tenant-customized instructions that must be overwritten.')]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/ai/database/migrations/2026_05_15_201835_tmp_update_ai_support_assistant_default_instructions.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $payload = DB::table('settings')
                    ->where('group', 'ai-support-assistant')
                    ->where('name', 'instructions')
                    ->value('payload');

                expect(json_decode($payload, true))->toBe(AiSupportAssistantSettings::defaultInstructions());
            }
        );
    });
});

describe('2026_06_02_143001_add_citext_unique_to_departments_name', function () {
    it('renames case-insensitive duplicates and converts the name column to citext', function () {
        isolatedMigration(
            '2026_06_02_143001_add_citext_unique_to_departments_name',
            function () {
                Department::factory()->create(['name' => 'Sales', 'created_at' => now()->subMinutes(2)]);
                Department::factory()->create(['name' => 'sales', 'created_at' => now()->subMinute()]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/department/database/migrations/2026_06_02_143001_add_citext_unique_to_departments_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $names = DB::table('departments')->pluck('name');

                expect(columnNativeType('departments', 'name'))->toBe('citext')
                    ->and($names)->toContain('Sales')
                    ->and($names)->toContain('sales-2');
            }
        );
    });
});

describe('2026_06_02_143002_add_citext_unique_to_roles_name', function () {
    it('renames case-insensitive duplicates per guard and converts the name column to citext', function () {
        isolatedMigration(
            '2026_06_02_143002_add_citext_unique_to_roles_name',
            function () {
                Role::factory()->create(['name' => 'Editor', 'guard_name' => 'web', 'created_at' => now()->subMinutes(2)]);
                Role::factory()->create(['name' => 'editor', 'guard_name' => 'web', 'created_at' => now()->subMinute()]);
                Role::factory()->create(['name' => 'editor', 'guard_name' => 'api', 'created_at' => now()->subMinute()]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/authorization/database/migrations/2026_06_02_143002_add_citext_unique_to_roles_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $webNames = DB::table('roles')->where('guard_name', 'web')->pluck('name');
                $apiNames = DB::table('roles')->where('guard_name', 'api')->pluck('name');

                expect(columnNativeType('roles', 'name'))->toBe('citext')
                    ->and($webNames)->toContain('Editor')
                    ->and($webNames)->toContain('editor-2')
                    ->and($apiNames)->toContain('editor');
            }
        );
    });
});

describe('2026_06_02_143003_add_citext_unique_to_users_email', function () {
    it('renames case-insensitive duplicates, preserves null emails and converts the email column to citext', function () {
        isolatedMigration(
            '2026_06_02_143003_add_citext_unique_to_users_email',
            function () {
                User::factory()->create(['email' => 'dup@example.com', 'created_at' => now()->subMinutes(2)]);
                User::factory()->create(['email' => 'DUP@EXAMPLE.COM', 'created_at' => now()->subMinute()]);
                User::factory()->count(2)->create(['email' => null]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'database/migrations/2026_06_02_143003_add_citext_unique_to_users_email.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $emails = DB::table('users')->pluck('email');

                expect(columnNativeType('users', 'email'))->toBe('citext')
                    ->and($emails)->toContain('dup@example.com')
                    ->and($emails)->toContain('DUP@EXAMPLE.COM-2')
                    ->and(DB::table('users')->whereNull('email')->count())->toBe(2);
            }
        );
    });
});

describe('2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name', function () {
    it('merges case-insensitive duplicates keeping the latest, reassigns its articles, soft deletes the rest and converts the name column to citext', function () {
        isolatedMigration(
            '2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name',
            function () {
                $oldest = KnowledgeBaseStatus::factory()->create(['name' => 'Published', 'created_at' => now()->subMinutes(3)]);
                $middle = KnowledgeBaseStatus::factory()->create(['name' => 'published', 'created_at' => now()->subMinutes(2)]);
                $latest = KnowledgeBaseStatus::factory()->create(['name' => 'PUBLISHED', 'created_at' => now()->subMinute()]);
                $unrelated = KnowledgeBaseStatus::factory()->create(['name' => 'Draft', 'created_at' => now()]);

                $articleOnOldest = KnowledgeBaseItem::factory()->create(['status_id' => $oldest->id]);
                $articleOnMiddle = KnowledgeBaseItem::factory()->create(['status_id' => $middle->id]);
                $articleOnLatest = KnowledgeBaseItem::factory()->create(['status_id' => $latest->id]);
                $articleOnUnrelated = KnowledgeBaseItem::factory()->create(['status_id' => $unrelated->id]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/knowledge-base/database/migrations/2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                // The latest duplicate is kept live; the two earlier ones are soft deleted.
                expect(columnNativeType('knowledge_base_statuses', 'name'))->toBe('citext')
                    ->and(KnowledgeBaseStatus::withTrashed()->find($latest->id)->deleted_at)->toBeNull()
                    ->and(KnowledgeBaseStatus::withTrashed()->find($oldest->id)->deleted_at)->not->toBeNull()
                    ->and(KnowledgeBaseStatus::withTrashed()->find($middle->id)->deleted_at)->not->toBeNull()
                    ->and(KnowledgeBaseStatus::withTrashed()->find($unrelated->id)->deleted_at)->toBeNull()
                    ->and(KnowledgeBaseStatus::query()->whereRaw('LOWER(name) = ?', ['published'])->count())->toBe(1);

                // Articles from the merged statuses are reassigned to the kept status; the unrelated one is left alone.
                expect(DB::table('knowledge_base_articles')->where('id', $articleOnOldest->id)->value('status_id'))->toBe($latest->id)
                    ->and(DB::table('knowledge_base_articles')->where('id', $articleOnMiddle->id)->value('status_id'))->toBe($latest->id)
                    ->and(DB::table('knowledge_base_articles')->where('id', $articleOnLatest->id)->value('status_id'))->toBe($latest->id)
                    ->and(DB::table('knowledge_base_articles')->where('id', $articleOnUnrelated->id)->value('status_id'))->toBe($unrelated->id);
            }
        );
    });
});

describe('2026_06_05_193725_add_citext_unique_to_service_request_statuses_name', function () {
    it('merges same-classification duplicates, suffix-renames cross-classification duplicates, reassigns relations and converts the name column to citext', function () {
        isolatedMigration(
            '2026_06_05_193725_add_citext_unique_to_service_request_statuses_name',
            function () {
                $division = Division::factory()->create();

                // Same name + same classification: should merge, keeping the latest (system protected) row.
                $olderTriage = ServiceRequestStatus::factory()->systemProtected()->create([
                    'name' => 'Triage',
                    'classification' => SystemServiceRequestClassification::Open,
                    'created_at' => now()->subMinutes(5),
                ]);
                $newerTriage = ServiceRequestStatus::factory()->systemProtected()->create([
                    'name' => 'triage',
                    'classification' => SystemServiceRequestClassification::Open,
                    'created_at' => now()->subMinute(),
                ]);

                $serviceRequest = ServiceRequest::factory()->create([
                    'status_id' => $olderTriage->id,
                    'division_id' => $division->id,
                ]);
                // Insert the assignment directly to bypass the manager-validation observer; the
                // migration reassigns this FK at the database level regardless.
                $assignmentId = Str::orderedUuid()->toString();
                DB::table('service_request_assignments')->insert([
                    'id' => $assignmentId,
                    'service_request_id' => $serviceRequest->id,
                    'user_id' => User::factory()->create()->id,
                    'assigned_at' => now(),
                    'service_request_status_id' => $olderTriage->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // History records the loser status id inside both JSON value columns.
                $historyId = Str::orderedUuid()->toString();
                DB::table('service_request_histories')->insert([
                    'id' => $historyId,
                    'service_request_id' => $serviceRequest->id,
                    'original_values' => json_encode(['status_id' => $olderTriage->id]),
                    'new_values' => json_encode(['status_id' => $olderTriage->id]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Same name + different classification: should be suffix-renamed, not merged.
                $openBacklog = ServiceRequestStatus::factory()->create([
                    'name' => 'Backlog',
                    'classification' => SystemServiceRequestClassification::Open,
                    'created_at' => now()->subMinutes(2),
                ]);
                $closedBacklog = ServiceRequestStatus::factory()->create([
                    'name' => 'Backlog',
                    'classification' => SystemServiceRequestClassification::Closed,
                    'created_at' => now()->subMinute(),
                ]);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_06_05_193725_add_citext_unique_to_service_request_statuses_name.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                // Same-classification duplicates merged: latest kept, older soft deleted, relations reassigned.
                expect(columnNativeType('service_request_statuses', 'name'))->toBe('citext')
                    ->and(ServiceRequestStatus::withTrashed()->find($newerTriage->id)->deleted_at)->toBeNull()
                    ->and(ServiceRequestStatus::withTrashed()->find($olderTriage->id)->deleted_at)->not->toBeNull()
                    ->and($serviceRequest->fresh()->status_id)->toBe($newerTriage->id)
                    ->and(DB::table('service_request_assignments')->where('id', $assignmentId)->value('service_request_status_id'))->toBe($newerTriage->id)
                    ->and(ServiceRequestStatus::query()->whereRaw('LOWER(name) = ?', ['triage'])->count())->toBe(1);

                // History JSON status_id references are repointed from the loser to the kept status.
                $history = DB::table('service_request_histories')->where('id', $historyId)->first();
                expect(json_decode($history->original_values, true)['status_id'])->toBe($newerTriage->id)
                    ->and(json_decode($history->new_values, true)['status_id'])->toBe($newerTriage->id);

                // Cross-classification duplicates preserved, but disambiguated by suffix.
                expect($closedBacklog->fresh()->name)->toBe('Backlog')
                    ->and($openBacklog->fresh()->name)->toBe('Backlog-2')
                    ->and($closedBacklog->fresh()->deleted_at)->toBeNull()
                    ->and($openBacklog->fresh()->deleted_at)->toBeNull();
            }
        );
    });
});
