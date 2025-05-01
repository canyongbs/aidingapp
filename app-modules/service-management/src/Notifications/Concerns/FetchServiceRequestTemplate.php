<?php

namespace AidingApp\ServiceManagement\Notifications\Concerns;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;

trait FetchServiceRequestTemplate
{
    public function fetchTemplate(
        ServiceRequestType $serviceRequestType,
        ServiceRequestEmailTemplateType $templateType,
        ServiceRequestTypeEmailTemplateRole $templateRole
    ): ?ServiceRequestTypeEmailTemplate {
        return ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $serviceRequestType->getKey())
            ->where('type', $templateType)
            ->where('role', $templateRole)
            ->first();
    }
}
