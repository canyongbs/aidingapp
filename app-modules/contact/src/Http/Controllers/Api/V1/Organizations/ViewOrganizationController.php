<?php

namespace AidingApp\Contact\Http\Controllers\Api\V1\Organizations;

use AidingApp\Contact\Http\Resources\Api\V1\OrganizationResource;
use AidingApp\Contact\Models\Organization;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class ViewOrganizationController
{
    /**
     * @response OrganizationResource
     */
    #[Group('Organizations')]
    public function __invoke(Organization $organization): JsonResource
    {
        Gate::authorize('viewAny', Organization::class);
        Gate::authorize('view', $organization);

        $organization->loadMissing(['industry', 'type']);

        return $organization
            ->toResource(OrganizationResource::class);
    }
}
