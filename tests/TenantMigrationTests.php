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
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
use App\Models\User;
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

describe('2026_06_02_122811_tmp_data_migrate_service_request_type_email_preferences', function () {
    it('migrates all boolean preference columns into the email preference table with no data loss', function () {
        isolatedMigration(
            '2026_06_02_122811_tmp_data_migrate_service_request_type_email_preferences',
            function () {
                $typeA = ServiceRequestType::factory()->create();
                $typeB = ServiceRequestType::factory()->create();

                $columnMap = [
                    'is_managers_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Email->value],
                    'is_managers_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Email->value],
                    'is_managers_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Email->value],
                    'is_managers_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Email->value],
                    'is_managers_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Email->value],
                    'is_auditors_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Email->value],
                    'is_auditors_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Email->value],
                    'is_auditors_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Email->value],
                    'is_auditors_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Email->value],
                    'is_auditors_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Email->value],
                    'is_customers_survey_response_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::SurveyResponse->value, ServiceRequestNotificationChannel::Email->value],
                    'is_managers_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_managers_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_managers_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_managers_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_managers_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_auditors_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_auditors_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_auditors_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_auditors_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_auditors_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_customers_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Created->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_customers_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Assigned->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_customers_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Update->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_customers_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
                    'is_customers_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Closed->value, ServiceRequestNotificationChannel::Notification->value],
                ];

                $valuesForTypeA = [];
                $valuesForTypeB = [];

                foreach (array_keys($columnMap) as $index => $column) {
                    $valuesForTypeA[$column] = $index % 2 === 0;
                    $valuesForTypeB[$column] = $index % 3 === 0;
                }

                DB::table('service_request_types')->where('id', $typeA->id)->update($valuesForTypeA);
                DB::table('service_request_types')->where('id', $typeB->id)->update($valuesForTypeB);

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/service-management/database/migrations/2026_06_02_122811_tmp_data_migrate_service_request_type_email_preferences.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                $expectedCountPerType = count($columnMap);

                expect(DB::table('service_request_type_email_preference')->where('service_request_type_id', $typeA->id)->count())
                    ->toBe($expectedCountPerType)
                    ->and(DB::table('service_request_type_email_preference')->where('service_request_type_id', $typeB->id)->count())
                    ->toBe($expectedCountPerType);

                foreach ($columnMap as $column => [$role, $templateType, $channel]) {
                    $prefA = DB::table('service_request_type_email_preference')
                        ->where('service_request_type_id', $typeA->id)
                        ->where('service_request_email_template_role', $role)
                        ->where('service_request_email_template_type', $templateType)
                        ->where('notification_channel', $channel)
                        ->first();

                    expect($prefA)->not->toBeNull()
                        ->and((bool) $prefA->is_enabled)->toBe($valuesForTypeA[$column]);

                    $prefB = DB::table('service_request_type_email_preference')
                        ->where('service_request_type_id', $typeB->id)
                        ->where('service_request_email_template_role', $role)
                        ->where('service_request_email_template_type', $templateType)
                        ->where('notification_channel', $channel)
                        ->first();

                    expect($prefB)->not->toBeNull()
                        ->and((bool) $prefB->is_enabled)->toBe($valuesForTypeB[$column]);
                }

                expect(ServiceRequestTypeEmailPreferenceFeature::active())->toBeTrue();
            }
        );
    });
});
