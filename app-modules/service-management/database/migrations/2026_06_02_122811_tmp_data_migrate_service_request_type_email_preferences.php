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
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private array $columnMap = [
        // Managers — email
        'is_managers_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Created->value,      ServiceRequestNotificationChannel::Email->value],
        'is_managers_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Assigned->value,     ServiceRequestNotificationChannel::Email->value],
        'is_managers_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Update->value,       ServiceRequestNotificationChannel::Email->value],
        'is_managers_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Email->value],
        'is_managers_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Closed->value,       ServiceRequestNotificationChannel::Email->value],

        // Auditors — email
        'is_auditors_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Created->value,      ServiceRequestNotificationChannel::Email->value],
        'is_auditors_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Assigned->value,     ServiceRequestNotificationChannel::Email->value],
        'is_auditors_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Update->value,       ServiceRequestNotificationChannel::Email->value],
        'is_auditors_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Email->value],
        'is_auditors_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Closed->value,       ServiceRequestNotificationChannel::Email->value],

        // Customers — email
        'is_customers_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Created->value,       ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Assigned->value,      ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Update->value,        ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::StatusChange->value,  ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Closed->value,        ServiceRequestNotificationChannel::Email->value],
        'is_customers_survey_response_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::SurveyResponse->value, ServiceRequestNotificationChannel::Email->value],

        // Managers — notification
        'is_managers_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Created->value,      ServiceRequestNotificationChannel::Notification->value],
        'is_managers_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Assigned->value,     ServiceRequestNotificationChannel::Notification->value],
        'is_managers_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Update->value,       ServiceRequestNotificationChannel::Notification->value],
        'is_managers_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
        'is_managers_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Manager->value, ServiceRequestEmailTemplateType::Closed->value,       ServiceRequestNotificationChannel::Notification->value],

        // Auditors — notification
        'is_auditors_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Created->value,      ServiceRequestNotificationChannel::Notification->value],
        'is_auditors_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Assigned->value,     ServiceRequestNotificationChannel::Notification->value],
        'is_auditors_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Update->value,       ServiceRequestNotificationChannel::Notification->value],
        'is_auditors_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
        'is_auditors_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Auditor->value, ServiceRequestEmailTemplateType::Closed->value,       ServiceRequestNotificationChannel::Notification->value],

        // Customers — notification
        'is_customers_service_request_created_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Created->value,      ServiceRequestNotificationChannel::Notification->value],
        'is_customers_service_request_assigned_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Assigned->value,     ServiceRequestNotificationChannel::Notification->value],
        'is_customers_service_request_update_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Update->value,       ServiceRequestNotificationChannel::Notification->value],
        'is_customers_service_request_status_change_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::StatusChange->value, ServiceRequestNotificationChannel::Notification->value],
        'is_customers_service_request_closed_notification_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Closed->value,       ServiceRequestNotificationChannel::Notification->value],
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $selectColumns = array_merge(['id'], array_keys($this->columnMap));

            DB::table('service_request_types')
                ->select($selectColumns)
                ->lazyById(100)
                ->each(function (object $serviceRequestType) use ($now): void {
                    $rows = [];

                    foreach ($this->columnMap as $column => [$role, $templateType, $channel]) {
                        $rows[] = [
                            'id' => (string) Str::orderedUuid(),
                            'service_request_type_id' => $serviceRequestType->id,
                            'service_request_email_template_role' => $role,
                            'service_request_email_template_type' => $templateType,
                            'notification_channel' => $channel,
                            'is_enabled' => (bool) $serviceRequestType->{$column},
                            'created_at' => $now,
                            'updated_at' => $now,
                            'deleted_at' => null,
                        ];
                    }

                    DB::table('service_request_type_email_preference')->insert($rows);
                });

            ServiceRequestTypeEmailPreferenceFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ServiceRequestTypeEmailPreferenceFeature::deactivate();

            DB::table('service_request_type_email_preference')
                ->whereIn(
                    'service_request_type_id',
                    DB::table('service_request_types')->select('id'),
                )
                ->delete();
        });
    }
};
