<?php

namespace AidingApp\Contact\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\OrganizationType;

class OrganizationTypePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization_type.view-any',
            denyResponse: 'You do not have permission to view organization type.'
        );
    }

    public function view(Authenticatable $authenticatable, OrganizationType $organizationType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_type.*.view', "organization_type.{$organizationType->id}.view"],
            denyResponse: 'You do not have permission to view this organization type.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization_type.create',
            denyResponse: 'You do not have permission to create organization type.'
        );
    }

    public function update(Authenticatable $authenticatable, OrganizationType $organizationType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_type.*.update', "organization_type.{$organizationType->id}.update"],
            denyResponse: 'You do not have permission to update this organization type.'
        );
    }

    public function delete(Authenticatable $authenticatable, OrganizationType $organizationType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_type.*.delete', "organization_type.{$organizationType->id}.delete"],
            denyResponse: 'You do not have permission to delete this organization type.'
        );
    }

    public function restore(Authenticatable $authenticatable, OrganizationType $organizationType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_type.*.restore', "organization_type.{$organizationType->id}.restore"],
            denyResponse: 'You do not have permission to restore this organization type.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, OrganizationType $organizationType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_type.*.force-delete', "organization_type.{$organizationType->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this organization type.'
        );
    }
}
