<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'license_settings.manage' => 'License Settings',
        'audit.view_audit_settings' => 'Audit',
        'theme.view_theme_settings' => 'Theme',
        'inbound_webhook.*.view' => 'Inbound Webhook',
        'inbound_webhook.view-any' => 'Inbound Webhook',
        'portal.view_portal_settings' => 'Portal',
        'integration-google-analytics.view_google_analytics_settings' => 'Integration: Google Analytics',
        'integration-google-recaptcha.view_google_recaptcha_settings' => 'Integration: Google reCAPTCHA',
        'integration-microsoft-clarity.view_microsoft_clarity_settings' => 'Integration: Microsoft Clarity',
        'integration-twilio.view_twilio_settings' => 'Integration: Twilio',
        'integration-aws-ses-event-handling.view_ses_settings' => 'Integration: AWS SES Event Handling',
        'authorization.view_azure_sso_settings' => 'Authorization',
        'authorization.view_google_sso_settings' => 'Authorization',
        'amazon-s3.manage_s3_settings' => 'Amazon S3',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
