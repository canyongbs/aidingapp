<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Contact\Http\Resources\Api\V1;

use AidingApp\Contact\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Organization $resource
 */
class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone_number' => $this->resource->phone_number,
            'website' => $this->resource->website,
            'industry' => $this->resource->industry ? [
                'id' => $this->resource->industry->id,
                'name' => $this->resource->industry->name,
            ] : null,
            'type' => $this->resource->type ? [
                'id' => $this->resource->type->id,
                'name' => $this->resource->type->name,
            ] : null,
            'description' => $this->resource->description,
            'number_of_employees' => $this->resource->number_of_employees,
            'address' => $this->resource->address,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'postalcode' => $this->resource->postalcode,
            'country' => $this->resource->country,
            'linkedin_url' => $this->resource->linkedin_url,
            'facebook_url' => $this->resource->facebook_url,
            'twitter_url' => $this->resource->twitter_url,
            /** @var array<int, string> */
            'domains' => array_map(
                fn (array $domain): string => $domain['domain'],
                $this->resource->domains ?? [],
            ),
            'is_contact_generation_enabled' => $this->resource->is_contact_generation_enabled,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
