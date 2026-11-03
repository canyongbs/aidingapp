<?php

namespace AidingApp\Contact\Http\Controllers\Api\V1\Organizations;

use AidingApp\Contact\Actions\SaveOrganization;
use AidingApp\Contact\DataTransferObjects\OrganizationDataObject;
use AidingApp\Contact\Http\Requests\Api\V1\Organizations\CreateOrganizationRequest;
use AidingApp\Contact\Http\Resources\Api\V1\OrganizationResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

class CreateOrganizationController
{
    #[Group('Organizations')]
    public function __invoke(CreateOrganizationRequest $request, SaveOrganization $saveOrganization): JsonResponse
    {
        $organization = $saveOrganization(OrganizationDataObject::fromData($request->validated()));

        $organization->refresh()->load(['industry', 'type']);

        return $organization
            ->toResource(OrganizationResource::class)
            ->response()
            ->setStatusCode(201);
    }
}
