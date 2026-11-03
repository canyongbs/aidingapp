<?php

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
