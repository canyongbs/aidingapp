<?php

namespace AidingApp\ContractManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\ContractManagement\Models\Contract;

class ContractPolicy
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
            abilities: 'contract.view-any',
            denyResponse: 'You do not have permission to view contracts.'
        );
    }

    public function view(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["contract.{$contract->getKey()}.view"],
            denyResponse: 'You do not have permission to view this contract.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contract.create',
            denyResponse: 'You do not have permission to create contracts.'
        );
    }

    public function update(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["contract.{$contract->getKey()}.update"],
            denyResponse: 'You do not have permission to update this contract.'
        );
    }

    public function delete(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["contract.{$contract->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this contract.'
        );
    }

    public function restore(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["contract.{$contract->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this contract.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["contract.{$contract->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this contract.'
        );
    }
}
