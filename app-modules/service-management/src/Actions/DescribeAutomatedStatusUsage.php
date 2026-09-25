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

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Warns that archiving a status will not stop the service request types that apply it
 * automatically when a request is assigned.
 *
 * A type automates a status purely by holding it in `automated_status_id`; there is no separate
 * enabled flag on the table, as the form toggle nulls the column when it is switched off.
 */
class DescribeAutomatedStatusUsage
{
    private const MAX_NAMED_TYPES = 3;

    /**
     * @param Builder<ServiceRequestStatus> $statuses the statuses about to be archived
     *
     * @return string|null null when none of them are automated by a service request type
     */
    public function __invoke(Builder $statuses): ?string
    {
        $statusIds = $statuses->clone()->pluck($statuses->getModel()->getQualifiedKeyName());

        if ($statusIds->isEmpty()) {
            return null;
        }

        $typeNames = ServiceRequestType::query()
            ->whereIn('automated_status_id', $statusIds)
            ->orderBy('name')
            ->pluck('name');

        if ($typeNames->isEmpty()) {
            return null;
        }

        $statusPhrase = $this->describeStatuses($statusIds);

        $typePhrase = $typeNames->count() === 1
            ? '1 service request type'
            : "{$typeNames->count()} service request types";

        return "{$statusPhrase} used for automatic status changes by {$typePhrase}: {$this->listTypeNames($typeNames)}. Archiving will not stop that automation.";
    }

    /**
     * @param Collection<int, string> $statusIds
     */
    private function describeStatuses(Collection $statusIds): string
    {
        if ($statusIds->count() === 1) {
            return 'This status is';
        }

        $automatedCount = ServiceRequestStatus::query()
            ->whereKey($statusIds)
            ->whereHas('serviceRequestTypes')
            ->count();

        return $automatedCount === 1
            ? '1 of the selected statuses is'
            : "{$automatedCount} of the selected statuses are";
    }

    /**
     * @param Collection<int, string> $typeNames
     */
    private function listTypeNames(Collection $typeNames): string
    {
        $named = $typeNames->take(self::MAX_NAMED_TYPES);

        $remaining = $typeNames->count() - $named->count();

        if ($remaining === 0) {
            return $named->implode(', ');
        }

        return $named->implode(', ') . ($remaining === 1
            ? ' and 1 other'
            : " and {$remaining} others");
    }
}
