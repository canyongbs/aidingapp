<?php

namespace AidingApp\ServiceManagement\Policies;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ServiceMonitoringTargetPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'service_monitoring.view-any',
            denyResponse: 'You do not have permission to view any service monitorings.'
        );
    }

    public function view(Authenticatable $authenticatable, ServiceMonitoringTarget $serviceMonitoringTarget): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["service_monitoring.{$serviceMonitoringTarget->getKey()}.view"],
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

    public function update(Authenticatable $authenticatable, ServiceMonitoringTarget $serviceMonitoringTarget): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["service_monitoring.{$serviceMonitoringTarget->getKey()}.update"],
            denyResponse: 'You do not have permission to update this service monitoring.'
        );
    }

    public function delete(Authenticatable $authenticatable, ServiceMonitoringTarget $serviceMonitoringTarget): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["service_monitoring.{$serviceMonitoringTarget->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this service monitoring.'
        );
    }

    public function restore(Authenticatable $authenticatable, ServiceMonitoringTarget $serviceMonitoringTarget): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["service_monitoring.{$serviceMonitoringTarget->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this service monitoring.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ServiceMonitoringTarget $serviceMonitoringTarget): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["service_monitoring.{$serviceMonitoringTarget->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this service monitoring.'
        );
    }
}
