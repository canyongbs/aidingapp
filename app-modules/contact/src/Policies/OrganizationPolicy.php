<?php

namespace AidingApp\Contact\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use Laravel\Pennant\Feature as PennantFeature;

class OrganizationPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasLicense(Contact::getLicenseType())) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        if (PennantFeature::deactivate('organization')) {
            return Response::deny('This feature is not active currently.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization.view-any',
            denyResponse: 'You do not have permission to view organization.'
        );
    }

    public function view(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.view', "organization.{$organization->id}.view"],
            denyResponse: 'You do not have permission to view this organization.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization.create',
            denyResponse: 'You do not have permission to create organization.'
        );
    }

    public function update(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.update', "organization.{$organization->id}.update"],
            denyResponse: 'You do not have permission to update this organization.'
        );
    }

    public function delete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.delete', "organization.{$organization->id}.delete"],
            denyResponse: 'You do not have permission to delete this organization.'
        );
    }

    public function restore(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.restore', "organization.{$organization->id}.restore"],
            denyResponse: 'You do not have permission to restore this organization.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.force-delete', "organization.{$organization->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this organization.'
        );
    }
}
