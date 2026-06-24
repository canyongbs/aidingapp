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

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ContactTypeManagementFeature;
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
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

describe('2026_06_11_183351_add_is_default_to_contact_types_table', function () {
    it('converts legacy semantic contact type colors, adds is_default and activates the feature', function () {
        isolatedMigration(
            '2026_06_11_183351_add_is_default_to_contact_types_table',
            function () {
                $legacyColors = [
                    'info' => 'blue',
                    'warning' => 'amber',
                    'success' => 'green',
                    'danger' => 'red',
                    'primary' => 'gray',
                    'gray' => 'gray',
                ];

                $ids = [];

                foreach (array_keys($legacyColors) as $legacyColor) {
                    $id = (string) Str::orderedUuid();
                    $ids[$legacyColor] = $id;

                    DB::table('contact_types')->insert([
                        'id' => $id,
                        'classification' => 'new',
                        'name' => "Type {$legacyColor}",
                        'color' => $legacyColor,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                expect(ContactTypeManagementFeature::active())->toBeFalse();

                $migrate = Artisan::call('migrate', [
                    '--path' => 'app-modules/contact/database/migrations/2026_06_11_183351_add_is_default_to_contact_types_table.php',
                ]);

                expect($migrate)->toBe(Command::SUCCESS);

                foreach ($legacyColors as $legacyColor => $expectedColor) {
                    expect(DB::table('contact_types')->where('id', $ids[$legacyColor])->value('color'))
                        ->toBe($expectedColor)
                        ->and(DB::table('contact_types')->where('id', $ids[$legacyColor])->value('is_default'))
                        ->toBeFalsy();
                }

                expect(ContactTypeManagementFeature::active())->toBeTrue();
            }
        );
    });
});
