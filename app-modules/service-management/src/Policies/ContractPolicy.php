<?php

namespace AidingApp\ServiceManagement\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use AidingApp\Contact\Models\Contact;
use App\Support\FeatureAccessResponse;
use AidingApp\ServiceManagement\Models\Contract;

class ContractPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        if (! Gate::check(
            collect($this->requiredFeatures())->map(fn (Feature $feature) => $feature->getGateName())
        )) {
            return FeatureAccessResponse::deny();
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contract.view-any',
            denyResponse: 'You do not have permission to view assets.'
        );
    }

    public function view(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contract.*.view', "contract.{$contract->getKey()}.view"],
            denyResponse: 'You do not have permission to view this asset.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contract.create',
            denyResponse: 'You do not have permission to create assets.'
        );
    }

    public function update(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contract.*.update', "contract.{$contract->getKey()}.update"],
            denyResponse: 'You do not have permission to update this asset.'
        );
    }

    public function delete(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contract.*.delete', "contract.{$contract->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this asset.'
        );
    }

    public function restore(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contract.*.restore', "contract.{$contract->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this asset.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Contract $contract): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contract.*.force-delete', "contract.{$contract->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this asset.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ServiceManagement];
    }
}
