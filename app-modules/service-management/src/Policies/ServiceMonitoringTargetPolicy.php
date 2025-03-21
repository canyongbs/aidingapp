<?php

namespace AidingApp\ServiceManagement\Policies;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ServiceMonitoringTargetPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'service_monitoring.view-any',
            denyResponse: 'You do not have permission to view any service monitorings.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['service_monitoring.*.view'],
            denyResponse: 'You do not have permission to view this service monitoring.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'service_monitoring.create',
            denyResponse: 'You do not have permission to create service monitorings.'
        );
    }

    public function update(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['service_monitoring.*.update'],
            denyResponse: 'You do not have permission to update this service monitoring.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['service_monitoring.*.delete'],
            denyResponse: 'You do not have permission to delete this service monitoring.'
        );
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['service_monitoring.*.restore'],
            denyResponse: 'You do not have permission to restore this service monitoring.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['service_monitoring.*.force-delete'],
            denyResponse: 'You do not have permission to permanently delete this service monitoring.'
        );
    }
}
