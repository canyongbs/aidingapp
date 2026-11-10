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

namespace AidingApp\ServiceManagement\Http\Resources\Api\V1;

use AidingApp\Contact\Models\ContactType;
use AidingApp\ServiceManagement\Models\Advisory;
use App\Features\AdvisoryVisibilityFeature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Advisory $resource
 */
class AdvisoryResource extends JsonResource
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
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'severity' => $this->resource->severity ? [
                'id' => $this->resource->severity->id,
                'name' => $this->resource->severity->name,
                'color' => $this->resource->severity->color,
            ] : null,
            'status' => $this->resource->status ? [
                'id' => $this->resource->status->id,
                'name' => $this->resource->status->name,
                'classification' => $this->resource->status->classification,
            ] : null,
            'department' => $this->resource->assignedDepartment ? [
                'id' => $this->resource->assignedDepartment->id,
                'name' => $this->resource->assignedDepartment->name,
            ] : null,
            /** Who can see the advisory in the portal: everyone (`public`) or only signed-in contacts (`authenticated`). */
            'visibility' => $this->when(AdvisoryVisibilityFeature::active(), fn () => $this->resource->visibility),
            /** Whether only contacts of the `restricted_to_contact_types` can see an `authenticated` advisory. */
            'is_visibility_restricted' => $this->when(AdvisoryVisibilityFeature::active(), fn (): bool => $this->resource->is_visibility_restricted),
            'restricted_to_contact_types' => $this->when(AdvisoryVisibilityFeature::active(), fn (): array => $this->resource->restrictedToContactTypes
                ->sortBy('name')
                ->map(fn (ContactType $contactType): array => [
                    'id' => $contactType->id,
                    'name' => $contactType->name,
                ])
                ->values()
                ->all()),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
