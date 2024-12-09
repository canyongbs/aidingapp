<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'contact_status.view-any' => 'Contact Status',
        'contact_status.create' => 'Contact Status',
        'contact_status.*.view' => 'Contact Status',
        'contact_status.*.update' => 'Contact Status',
        'contact_status.*.delete' => 'Contact Status',
        'contact_status.*.restore' => 'Contact Status',
        'contact_status.*.force-delete' => 'Contact Status',
        'contact_source.view-any' => 'Contact Source',
        'contact_source.create' => 'Contact Source',
        'contact_source.*.view' => 'Contact Source',
        'contact_source.*.update' => 'Contact Source',
        'contact_source.*.delete' => 'Contact Source',
        'contact_source.*.restore' => 'Contact Source',
        'contact_source.*.force-delete' => 'Contact Source',
        'organization_type.view-any' => 'Organization Type',
        'organization_type.create' => 'Organization Type',
        'organization_type.*.view' => 'Organization Type',
        'organization_type.*.update' => 'Organization Type',
        'organization_type.*.delete' => 'Organization Type',
        'organization_type.*.restore' => 'Organization Type',
        'organization_type.*.force-delete' => 'Organization Type',
        'organization_industry.view-any' => 'Organization Industry',
        'organization_industry.create' => 'Organization Industry',
        'organization_industry.*.view' => 'Organization Industry',
        'organization_industry.*.update' => 'Organization Industry',
        'organization_industry.*.delete' => 'Organization Industry',
        'organization_industry.*.restore' => 'Organization Industry',
        'organization_industry.*.force-delete' => 'Organization Industry',
        'service_request_type.view-any' => 'Service Request Type',
        'service_request_type.create' => 'Service Request Type',
        'service_request_type.*.view' => 'Service Request Type',
        'service_request_type.*.update' => 'Service Request Type',
        'service_request_type.*.delete' => 'Service Request Type',
        'service_request_type.*.restore' => 'Service Request Type',
        'service_request_type.*.force-delete' => 'Service Request Type',
        'service_request_status.view-any' => 'Service Request Status',
        'service_request_status.create' => 'Service Request Status',
        'service_request_status.*.view' => 'Service Request Status',
        'service_request_status.*.update' => 'Service Request Status',
        'service_request_status.*.delete' => 'Service Request Status',
        'service_request_status.*.restore' => 'Service Request Status',
        'service_request_status.*.force-delete' => 'Service Request Status',
        'service_request_form.view-any' => 'Service Request Form',
        'service_request_form.create' => 'Service Request Form',
        'service_request_form.*.view' => 'Service Request Form',
        'service_request_form.*.update' => 'Service Request Form',
        'service_request_form.*.delete' => 'Service Request Form',
        'service_request_form.*.restore' => 'Service Request Form',
        'service_request_form.*.force-delete' => 'Service Request Form',
        'sla.view-any' => 'SLA',
        'sla.create' => 'SLA',
        'sla.*.view' => 'SLA',
        'sla.*.update' => 'SLA',
        'sla.*.delete' => 'SLA',
        'sla.*.restore' => 'SLA',
        'sla.*.force-delete' => 'SLA',
        'knowledge_base_category.view-any' => 'Knowledge Base Category',
        'knowledge_base_category.create' => 'Knowledge Base Category',
        'knowledge_base_category.*.view' => 'Knowledge Base Category',
        'knowledge_base_category.*.update' => 'Knowledge Base Category',
        'knowledge_base_category.*.delete' => 'Knowledge Base Category',
        'knowledge_base_category.*.restore' => 'Knowledge Base Category',
        'knowledge_base_category.*.force-delete' => 'Knowledge Base Category',
        'knowledge_base_quality.view-any' => 'Knowledge Base Quality',
        'knowledge_base_quality.create' => 'Knowledge Base Quality',
        'knowledge_base_quality.*.view' => 'Knowledge Base Quality',
        'knowledge_base_quality.*.update' => 'Knowledge Base Quality',
        'knowledge_base_quality.*.delete' => 'Knowledge Base Quality',
        'knowledge_base_quality.*.restore' => 'Knowledge Base Quality',
        'knowledge_base_quality.*.force-delete' => 'Knowledge Base Quality',
        'knowledge_base_status.view-any' => 'Knowledge Base Status',
        'knowledge_base_status.create' => 'Knowledge Base Status',
        'knowledge_base_status.*.view' => 'Knowledge Base Status',
        'knowledge_base_status.*.update' => 'Knowledge Base Status',
        'knowledge_base_status.*.delete' => 'Knowledge Base Status',
        'knowledge_base_status.*.restore' => 'Knowledge Base Status',
        'knowledge_base_status.*.force-delete' => 'Knowledge Base Status',
        'tag.view-any' => 'Tag',
        'tag.create' => 'Tag',
        'tag.*.view' => 'Tag',
        'tag.*.update' => 'Tag',
        'tag.*.delete' => 'Tag',
        'tag.*.restore' => 'Tag',
        'tag.*.force-delete' => 'Tag',
        'pronouns.view-any' => 'Pronouns',
        'pronouns.create' => 'Pronouns',
        'pronouns.*.view' => 'Pronouns',
        'pronouns.*.update' => 'Pronouns',
        'pronouns.*.delete' => 'Pronouns',
        'pronouns.*.restore' => 'Pronouns',
        'pronouns.*.force-delete' => 'Pronouns',
        'notification_setting.view-any' => 'Notification Setting',
        'notification_setting.create' => 'Notification Setting',
        'notification_setting.*.view' => 'Notification Setting',
        'notification_setting.*.update' => 'Notification Setting',
        'notification_setting.*.delete' => 'Notification Setting',
        'notification_setting.*.restore' => 'Notification Setting',
        'notification_setting.*.force-delete' => 'Notification Setting',
        'email_template.view-any' => 'Email Template',
        'email_template.create' => 'Email Template',
        'email_template.*.view' => 'Email Template',
        'email_template.*.update' => 'Email Template',
        'email_template.*.delete' => 'Email Template',
        'email_template.*.restore' => 'Email Template',
        'email_template.*.force-delete' => 'Email Template',
        'sms_template.view-any' => 'SMS Template',
        'sms_template.create' => 'SMS Template',
        'sms_template.*.view' => 'SMS Template',
        'sms_template.*.update' => 'SMS Template',
        'sms_template.*.delete' => 'SMS Template',
        'sms_template.*.restore' => 'SMS Template',
        'sms_template.*.force-delete' => 'SMS Template',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
