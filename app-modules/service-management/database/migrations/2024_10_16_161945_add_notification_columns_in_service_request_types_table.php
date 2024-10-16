<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->boolean('is_managers_service_request_created_email_enabled')->default(false);
            $table->boolean('is_managers_service_request_created_notification_enabled')->default(false);
            $table->boolean('is_managers_service_request_assigned_email_enabled')->default(false);
            $table->boolean('is_managers_service_request_assigned_notification_enabled')->default(false);
            $table->boolean('is_managers_service_request_resolved_email_enabled')->default(false);
            $table->boolean('is_managers_service_request_resolved_notification_enabled')->default(false);
            $table->boolean('is_auditors_service_request_created_email_enabled')->default(false);
            $table->boolean('is_auditors_service_request_created_notification_enabled')->default(false);
            $table->boolean('is_auditors_service_request_assigned_email_enabled')->default(false);
            $table->boolean('is_auditors_service_request_assigned_notification_enabled')->default(false);
            $table->boolean('is_auditors_service_request_resolved_email_enabled')->default(false);
            $table->boolean('is_auditors_service_request_resolved_notification_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn('is_managers_service_request_created_email_enabled');
            $table->dropColumn('is_managers_service_request_created_notification_enabled');
            $table->dropColumn('is_managers_service_request_assigned_email_enabled');
            $table->dropColumn('is_managers_service_request_assigned_notification_enabled');
            $table->dropColumn('is_managers_service_request_resolved_email_enabled');
            $table->dropColumn('is_managers_service_request_resolved_notification_enabled');

            $table->dropColumn('is_auditors_service_request_created_email_enabled');
            $table->dropColumn('is_auditors_service_request_created_notification_enabled');
            $table->dropColumn('is_auditors_service_request_assigned_email_enabled');
            $table->dropColumn('is_auditors_service_request_assigned_notification_enabled');
            $table->dropColumn('is_auditors_service_request_resolved_email_enabled');
            $table->dropColumn('is_auditors_service_request_resolved_notification_enabled');
        });
    }
};
