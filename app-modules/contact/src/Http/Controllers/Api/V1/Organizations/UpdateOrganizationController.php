<?php

namespace AidingApp\Contact\Http\Controllers\Api\V1\Organizations;

use AidingApp\Contact\Actions\SaveOrganization;
use AidingApp\Contact\DataTransferObjects\OrganizationDataObject;
use AidingApp\Contact\Http\Requests\Api\V1\Organizations\UpdateOrganizationRequest;
use AidingApp\Contact\Http\Resources\Api\V1\OrganizationResource;
use AidingApp\Contact\Models\Organization;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateOrganizationController
{
    /**
     * @response OrganizationResource
     */
    #[Group('Organizations')]
    public function __invoke(UpdateOrganizationRequest $request, SaveOrganization $saveOrganization, Organization $organization): JsonResource
    {
        $organization = $saveOrganization(OrganizationDataObject::fromData($request->validated()), $organization);

        $organization->refresh()->load(['industry', 'type']);

        return $organization
            ->toResource(OrganizationResource::class);
    }
}
