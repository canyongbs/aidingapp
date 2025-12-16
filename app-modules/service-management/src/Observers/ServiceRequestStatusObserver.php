<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Illuminate\Support\Facades\DB;

class ServiceRequestStatusObserver
{
    public function creating(ServiceRequestStatus $serviceRequestStatus): void
    {
        if (! isset($serviceRequestStatus->sort)) {
            $serviceRequestStatus->setAttribute(
                'sort',
                DB::raw('(SELECT COALESCE(MAX(service_request_statuses.sort), 0) + 1 FROM service_request_statuses)')
            );
        }
    }

    public function updating(ServiceRequestStatus $serviceRequestStatus): void
    {
        if ($serviceRequestStatus->isDirty('sort') && isset($serviceRequestStatus->sort)) {
            $maxSort = ServiceRequestStatus::count();

            if ($serviceRequestStatus->sort > $maxSort) {
                $serviceRequestStatus->sort = $maxSort;
            }

            if ($serviceRequestStatus->sort < 1) {
                $serviceRequestStatus->sort = 1;
            }

            $this->reorderStatusItems($serviceRequestStatus);
        }
    }

    public function deleted(ServiceRequestStatus $serviceRequestStatus): void
    {
        $this->reorderAllItems();
    }

    public function restored(ServiceRequestStatus $serviceRequestStatus): void
    {
        $serviceRequestStatus->setAttribute(
            'sort',
            DB::raw('(SELECT COALESCE(MAX(service_request_statuses.sort), 0) + 1 FROM service_request_statuses)')
        );
        $serviceRequestStatus->saveQuietly();
    }

    protected function reorderStatusItems(ServiceRequestStatus $updatingStatus): void
    {
        DB::transaction(function () use ($updatingStatus) {
            $oldSort = $updatingStatus->getOriginal('sort');
            $newSort = $updatingStatus->sort;

            if ($oldSort === $newSort) {
                return;
            }

            if ($newSort > $oldSort) {
                // Moving down: shift items up between old and new position
                $statusesToUpdate = ServiceRequestStatus::whereBetween('sort', [$oldSort + 1, $newSort])
                    ->where('id', '!=', $updatingStatus->id)
                    ->orderBy('sort')
                    ->pluck('id')
                    ->toArray();

                if (! empty($statusesToUpdate)) {
                    $caseStatements = collect($statusesToUpdate)
                        ->map(function (string $id, int $index) use ($oldSort) {
                            return "WHEN id = '{$id}' THEN " . ($oldSort + $index);
                        })
                        ->join(' ');

                    ServiceRequestStatus::whereIn('id', $statusesToUpdate)
                        ->update(['sort' => DB::raw("(CASE {$caseStatements} END)")]);
                }
            } else {
                // Moving up: shift items down between new and old position
                $statusesToUpdate = ServiceRequestStatus::whereBetween('sort', [$newSort, $oldSort - 1])
                    ->where('id', '!=', $updatingStatus->id)
                    ->orderBy('sort')
                    ->pluck('id')
                    ->toArray();

                if (! empty($statusesToUpdate)) {
                    $caseStatements = collect($statusesToUpdate)
                        ->map(function (string $id, int $index) use ($newSort) {
                            return "WHEN id = '{$id}' THEN " . ($newSort + $index + 1);
                        })
                        ->join(' ');

                    ServiceRequestStatus::whereIn('id', $statusesToUpdate)
                        ->update(['sort' => DB::raw("(CASE {$caseStatements} END)")]);
                }
            }
        });
    }

    protected function reorderAllItems(): void
    {
        DB::transaction(function () {
            $statuses = ServiceRequestStatus::orderBy('sort')->pluck('id')->toArray();

            if (! empty($statuses)) {
                $caseStatements = collect($statuses)
                    ->map(fn (string $id, int $index) => "WHEN id = '{$id}' THEN " . ($index + 1))
                    ->join(' ');

                ServiceRequestStatus::whereIn('id', $statuses)
                    ->update(['sort' => DB::raw("(CASE {$caseStatements} END)")]);
            }
        });
    }
}
