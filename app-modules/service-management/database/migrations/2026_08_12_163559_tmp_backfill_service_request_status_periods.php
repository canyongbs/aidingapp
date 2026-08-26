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

use App\Features\SlaWaitingExclusionFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            $classifications = DB::table('service_request_statuses')
                ->pluck('classification', 'id')
                ->all();

            DB::table('service_requests')
                ->select(['id', 'status_id', 'created_at'])
                ->whereNotExists(function (Builder $query): void {
                    $query->select(DB::raw(1))
                        ->from('service_request_status_periods')
                        ->whereColumn('service_request_status_periods.service_request_id', 'service_requests.id');
                })
                ->orderBy('id')
                ->chunkById(200, function (Collection $serviceRequests) use ($classifications): void {
                    $rows = [];

                    foreach ($serviceRequests as $serviceRequest) {
                        $histories = DB::table('service_request_histories')
                            ->where('service_request_id', $serviceRequest->id)
                            ->whereNull('deleted_at')
                            ->orderBy('created_at')
                            ->orderBy('id')
                            ->get(['new_values', 'original_values', 'created_at']);

                        $periods = $this->buildPeriods($serviceRequest, $histories);

                        $previousStatusId = null;

                        foreach ($periods as $period) {
                            if ($period['status_id'] === $previousStatusId) {
                                continue;
                            }

                            $previousStatusId = $period['status_id'];

                            // A status_id absent from the map was hard-deleted; record the period with an
                            // unknown (null) classification so the span is not silently merged into the
                            // previous period, and is treated as active/non-excluded time.
                            $statusExists = array_key_exists($period['status_id'], $classifications);

                            $rows[] = [
                                'id' => (string) Str::uuid(),
                                'service_request_id' => $serviceRequest->id,
                                'service_request_status_id' => $statusExists ? $period['status_id'] : null,
                                'classification' => $statusExists ? $classifications[$period['status_id']] : null,
                                'started_at' => $period['started_at'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if ($rows !== []) {
                        DB::table('service_request_status_periods')->insert($rows);
                    }
                });

            SlaWaitingExclusionFeature::activate();
        });
    }

    public function down(): void
    {
        SlaWaitingExclusionFeature::deactivate();
    }

    /**
     * @param Collection<int, stdClass> $histories
     *
     * @return array<int, array{status_id: string, started_at: mixed}>
     */
    private function buildPeriods(object $serviceRequest, Collection $histories): array
    {
        $periods = [];

        $initialStatusId = $this->resolveInitialStatusId($histories, $serviceRequest->status_id);

        if ($initialStatusId !== null) {
            $periods[] = [
                'status_id' => $initialStatusId,
                'started_at' => $serviceRequest->created_at,
            ];
        }

        foreach ($histories as $history) {
            $originalValues = json_decode($history->original_values, true) ?? [];
            $newValues = json_decode($history->new_values, true) ?? [];

            // Empty `original_values` marks the creation event; its status is already the initial period.
            if (empty($originalValues)) {
                continue;
            }

            if (! array_key_exists('status_id', $newValues) || $newValues['status_id'] === null) {
                continue;
            }

            $periods[] = [
                'status_id' => $newValues['status_id'],
                'started_at' => $history->created_at,
            ];
        }

        return $periods;
    }

    /**
     * @param Collection<int, stdClass> $histories
     */
    private function resolveInitialStatusId(Collection $histories, ?string $currentStatusId): ?string
    {
        foreach ($histories as $history) {
            $originalValues = json_decode($history->original_values, true) ?? [];
            $newValues = json_decode($history->new_values, true) ?? [];

            if (empty($originalValues) && array_key_exists('status_id', $newValues)) {
                return $newValues['status_id'];
            }
        }

        foreach ($histories as $history) {
            $originalValues = json_decode($history->original_values, true) ?? [];

            if (array_key_exists('status_id', $originalValues) && $originalValues['status_id'] !== null) {
                return $originalValues['status_id'];
            }
        }

        return $currentStatusId;
    }
};
