<?php

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     * Maps each legacy boolean column on service_request_types to its
     * corresponding (role, type, channel) tuple in the pivot table.
     *
     * Tuple: [ServiceRequestTypeEmailTemplateRole value, ServiceRequestEmailTemplateType value, ServiceRequestNotificationChannel value]
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private array $columnMap = [
        // --- Email channel ---

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

        // Customers — email (survey_response is email-only; managers/auditors have no survey_response email column)
        'is_customers_service_request_created_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Created->value,       ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_assigned_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Assigned->value,      ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_update_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Update->value,        ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_status_change_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::StatusChange->value,  ServiceRequestNotificationChannel::Email->value],
        'is_customers_service_request_closed_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::Closed->value,        ServiceRequestNotificationChannel::Email->value],
        'is_customers_survey_response_email_enabled' => [ServiceRequestTypeEmailTemplateRole::Customer->value, ServiceRequestEmailTemplateType::SurveyResponse->value, ServiceRequestNotificationChannel::Email->value],

        // --- Notification (in-app) channel ---
        // survey_response has no notification column for any role.

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

            // lazyById streams rows in chunks of 100, avoiding memory issues on large datasets.
            // DB::table() queries the raw table, so soft-deleted records are included automatically.
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
            // Remove all preference rows whose service_request_type_id still exists
            // (covers both active and soft-deleted service request types).
            DB::table('service_request_type_email_preference')
                ->whereIn(
                    'service_request_type_id',
                    DB::table('service_request_types')->select('id'),
                )
                ->delete();
        });
    }
};
