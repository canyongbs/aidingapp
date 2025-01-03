<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->renameColumn('is_managers_service_request_resolved_email_enabled', 'is_managers_service_request_closed_email_enabled');
            $table->renameColumn('is_managers_service_request_resolved_notification_enabled', 'is_managers_service_request_closed_notification_enabled');
            $table->renameColumn('is_auditors_service_request_resolved_email_enabled', 'is_auditors_service_request_closed_email_enabled');
            $table->renameColumn('is_auditors_service_request_resolved_notification_enabled', 'is_auditors_service_request_closed_notification_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->renameColumn('is_managers_service_request_closed_email_enabled', 'is_managers_service_request_resolved_email_enabled');
            $table->renameColumn('is_managers_service_request_closed_notification_enabled', 'is_managers_service_request_resolved_notification_enabled');
            $table->renameColumn('is_auditors_service_request_closed_email_enabled', 'is_auditors_service_request_resolved_email_enabled');
            $table->renameColumn('is_auditors_service_request_closed_notification_enabled', 'is_auditors_service_request_resolved_notification_enabled');
        });
    }
};
