<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->boolean('is_managers_service_request_update_email_enabled')->after('is_managers_service_request_resolved_notification_enabled')->default(false);
            $table->boolean('is_managers_service_request_update_notification_enabled')->after('is_managers_service_request_update_email_enabled')->default(false);
            $table->boolean('is_managers_service_request_status_change_email_enabled')->after('is_managers_service_request_update_notification_enabled')->default(false);
            $table->boolean('is_managers_service_request_status_change_notification_enabled')->after('is_managers_service_request_status_change_email_enabled')->default(false);
            $table->boolean('is_auditors_service_request_update_email_enabled')->after('is_auditors_service_request_resolved_notification_enabled')->default(false);
            $table->boolean('is_auditors_service_request_update_notification_enabled')->after('is_auditors_service_request_update_email_enabled')->default(false);
            $table->boolean('is_auditors_service_request_status_change_email_enabled')->after('is_auditors_service_request_update_notification_enabled')->default(false);
            $table->boolean('is_auditors_service_request_status_change_notification_enabled')->after('is_auditors_service_request_status_change_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_created_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_created_notification_enabled')->default(false);
            $table->boolean('is_customers_service_request_assigned_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_assigned_notification_enabled')->default(false);
            $table->boolean('is_customers_service_request_update_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_update_notification_enabled')->default(false);
            $table->boolean('is_customers_service_request_status_change_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_status_change_notification_enabled')->default(false);
            $table->boolean('is_customers_service_request_closed_email_enabled')->default(false);
            $table->boolean('is_customers_service_request_closed_notification_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_managers_service_request_update_email_enabled',
                'is_managers_service_request_update_notification_enabled',
                'is_managers_service_request_status_change_email_enabled',
                'is_managers_service_request_status_change_notification_enabled',
                'is_auditors_service_request_update_email_enabled',
                'is_auditors_service_request_update_notification_enabled',
                'is_auditors_service_request_status_change_email_enabled',
                'is_auditors_service_request_status_change_notification_enabled',
                'is_customers_service_request_created_email_enabled',
                'is_customers_service_request_created_notification_enabled',
                'is_customers_service_request_assigned_email_enabled',
                'is_customers_service_request_assigned_notification_enabled',
                'is_customers_service_request_update_email_enabled',
                'is_customers_service_request_update_notification_enabled',
                'is_customers_service_request_status_change_email_enabled',
                'is_customers_service_request_status_change_notification_enabled',
                'is_customers_service_request_closed_email_enabled',
                'is_customers_service_request_closed_notification_enabled',
            ]);
        });
    }
};
