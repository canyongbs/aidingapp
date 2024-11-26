<?php

namespace AidingApp\ContractManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\ContractManagement\Models\ContractType;

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
            denyResponse: 'You do not have permission to view contract types.'
        );
    }

    public function view(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$contractType->getKey()}.view"],
            denyResponse: 'You do not have permission to view this contract type.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create contract types.'
        );
    }

    public function update(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$contractType->getKey()}.update"],
            denyResponse: 'You do not have permission to update this contract type.'
        );
    }

    public function delete(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$contractType->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this contract type.'
        );
    }

    public function restore(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$contractType->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this contract type.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ContractType $contractType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$contractType->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this contract type.'
        );
    }
}
