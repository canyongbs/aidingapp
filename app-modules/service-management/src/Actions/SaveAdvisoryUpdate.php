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

use AidingApp\ServiceManagement\DataTransferObjects\AdvisoryUpdateDataObject;
use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class SaveAdvisoryUpdate
{
    /**
     * Adds an update to the advisory, or changes the given update, and moves the advisory to the given status in the
     * same transaction. Only attributes present on the data object are written, and a new update defaults to public
     * and to the current date.
     *
     * The advisory is re-read under a row lock, so it cannot be resolved by a concurrent request after it was checked.
     *
     * @throws ValidationException When the advisory is resolved, or the status no longer resolves.
     */
    public function __invoke(AdvisoryUpdateDataObject $data, Advisory $advisory, ?AdvisoryUpdate $existing = null): AdvisoryUpdate
    {
        $statusId = $data->status instanceof Optional ? null : $this->resolveStatusId($data->status);

        return DB::transaction(function () use ($data, $advisory, $existing, $statusId): AdvisoryUpdate {
            $advisory = Advisory::query()->whereKey($advisory->getKey())->lockForUpdate()->firstOrFail();

            if ($advisory->status?->classification === SystemAdvisoryStatusClassification::Resolved) {
                throw ValidationException::withMessages([
                    'advisory' => 'Updates cannot be added to or changed on a resolved advisory.',
                ]);
            }

            $attributes = array_filter([
                'title' => $data->title,
                'update' => $data->update,
                'internal' => $data->internal,
                'date' => $data->date,
            ], fn (mixed $value): bool => ! $value instanceof Optional);

            if ($existing === null) {
                $attributes += ['internal' => false, 'date' => now()];
            }

            $advisoryUpdate = $existing ?? $advisory->advisoryUpdates()->make();
            $advisoryUpdate->fill($attributes);
            $advisoryUpdate->save();

            if ($statusId !== null) {
                $advisory->status()->associate($statusId);
                $advisory->save();
            }

            return $advisoryUpdate;
        });
    }

    /**
     * Statuses are referenced by name, which may not be unique while the uniqueness flag is inactive, so an
     * ambiguous name is reported rather than guessed.
     *
     * TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): once the flag is removed, every name is unique,
     * so look the ID up with `value('id')` and drop the `limit(2)` and the ambiguous-name error.
     *
     * @throws ValidationException
     */
    private function resolveStatusId(string $name): string
    {
        $ids = AdvisoryStatus::query()->where('name', $name)->limit(2)->pluck('id');

        if ($ids->count() !== 1) {
            throw ValidationException::withMessages([
                'status' => $ids->isEmpty()
                    ? 'The specified status does not exist.'
                    : 'The specified status name is ambiguous.',
            ]);
        }

        $id = $ids->first();

        assert(is_string($id));

        return $id;
    }
}
