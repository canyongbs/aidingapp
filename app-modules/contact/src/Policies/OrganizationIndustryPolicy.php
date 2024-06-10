<?php

namespace AidingApp\Contact\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\OrganizationIndustry;

class OrganizationIndustryPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization_industry.view-any',
            denyResponse: 'You do not have permission to view organization industry.'
        );
    }

    public function view(Authenticatable $authenticatable, OrganizationIndustry $organizationIndustry): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_industry.*.view', "organization_industry.{ $organizationIndustry->id}.view"],
            denyResponse: 'You do not have permission to view this organization industry.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization_industry.create',
            denyResponse: 'You do not have permission to create organization industry.'
        );
    }

    public function update(Authenticatable $authenticatable, OrganizationIndustry $organizationIndustry): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_industry.*.update', "organization_industry.{ $organizationIndustry->id}.update"],
            denyResponse: 'You do not have permission to update this organization industry.'
        );
    }

    public function delete(Authenticatable $authenticatable, OrganizationIndustry $organizationIndustry): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_industry.*.delete', "organization_industry.{ $organizationIndustry->id}.delete"],
            denyResponse: 'You do not have permission to delete this organization industry.'
        );
    }

    public function restore(Authenticatable $authenticatable, OrganizationIndustry $organizationIndustry): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_industry.*.restore', "organization_industry.{ $organizationIndustry->id}.restore"],
            denyResponse: 'You do not have permission to restore this organization industry.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, OrganizationIndustry $organizationIndustry): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization_industry.*.force-delete', "organization_industry.{ $organizationIndustry->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this organization industry.'
        );
    }
}
