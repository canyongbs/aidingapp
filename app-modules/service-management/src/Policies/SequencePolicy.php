<?php

namespace AidingApp\ServiceManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class SequencePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.view-any',
            denyResponse: 'You do not have permission to view any service monitorings.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.view'],
            denyResponse: 'You do not have permission to view this service monitoring.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.create',
            denyResponse: 'You do not have permission to create service monitorings.'
        );
    }

    public function update(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.update'],
            denyResponse: 'You do not have permission to update this service monitoring.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.delete'],
            denyResponse: 'You do not have permission to delete this service monitoring.'
        );
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.restore'],
            denyResponse: 'You do not have permission to restore this service monitoring.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.force-delete'],
            denyResponse: 'You do not have permission to permanently delete this service monitoring.'
        );
    }
}
