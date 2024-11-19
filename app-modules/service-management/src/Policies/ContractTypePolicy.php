<?php

namespace AidingApp\ServiceManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ContractType;

class ContractTypePolicy
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
            abilities: 'product_admin.view-any',
            denyResponse: 'You do not have permission to view assets.'
        );
    }

    public function view(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['product_admin.*.view', "product_admin.{$contractType->getKey()}.view"],
            denyResponse: 'You do not have permission to view this asset.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create assets.'
        );
    }

    public function update(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['product_admin.*.update', "product_admin.{$contractType->getKey()}.update"],
            denyResponse: 'You do not have permission to update this asset.'
        );
    }

    public function delete(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['product_admin.*.delete', "product_admin.{$contractType->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this asset.'
        );
    }

    public function restore(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['product_admin.*.restore', "product_admin.{$contractType->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this asset.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['product_admin.*.force-delete', "product_admin.{$contractType->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this asset.'
        );
    }
}
