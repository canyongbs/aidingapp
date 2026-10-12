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

namespace AidingApp\Portal\DataTransferObjects;

use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class AdvisoryDetailData extends Data
{
    /**
     * @param list<AdvisoryUpdateData> $updates The advisory's updates, newest first.
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public ?AdvisorySeverityData $severity,
        public ?AdvisoryStatusData $status,
        public ?string $departmentName,
        public ?string $createdAt,
        public string $lastUpdatedAt,
        public array $updates,
    ) {}

    /**
     * Expects the advisory to have been queried with the `WithLastUpdatedAt` scope and its updates loaded newest first.
     */
    public static function fromModel(Advisory $advisory): self
    {
        return new self(
            id: $advisory->getKey(),
            title: $advisory->title,
            description: $advisory->description,
            severity: $advisory->severity ? AdvisorySeverityData::fromModel($advisory->severity) : null,
            status: $advisory->status ? AdvisoryStatusData::fromModel($advisory->status) : null,
            departmentName: $advisory->assignedDepartment?->name,
            createdAt: $advisory->created_at?->toIso8601String(),
            lastUpdatedAt: $advisory->getLastUpdatedAt()->toIso8601String(),
            updates: $advisory->advisoryUpdates
                ->map(fn (AdvisoryUpdate $advisoryUpdate): AdvisoryUpdateData => AdvisoryUpdateData::fromModel($advisoryUpdate))
                ->values()
                ->all(),
        );
    }
}
