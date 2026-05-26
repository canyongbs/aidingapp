<?php

namespace AidingApp\ServiceManagement\Http\Resources\Api\V1;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ServiceRequest $resource
 */
class ServiceRequestResource extends JsonResource
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
            'service_request_number' => $this->resource->service_request_number,
            'title' => $this->resource->title,
            'status_id' => $this->resource->status_id,
            'status_name' => $this->resource->status?->name,
            'priority_id' => $this->resource->priority_id,
            'priority_name' => $this->resource->priority?->name,
            'assignee_id' => $this->resource->assignedTo?->user_id,
            'assignee_name' => $this->resource->assignedTo?->user?->name,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
