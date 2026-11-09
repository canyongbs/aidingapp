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

use AidingApp\Contact\Models\ContactType;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\DataTransferObjects\AdvisoryDataObject;
use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Features\AdvisoryVisibilityFeature;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class SaveAdvisory
{
    /**
     * Creates an advisory, or updates the given one. Only attributes present on the data object are written.
     *
     * An existing advisory is re-read under a row lock, so concurrent partial updates cannot together leave a
     * restricted advisory without contact types.
     *
     * @throws ValidationException When a referenced record no longer resolves, or a restricted advisory would have no contact types.
     */
    public function __invoke(AdvisoryDataObject $data, ?Advisory $existing = null): Advisory
    {
        $errors = [];

        $attributes = array_filter([
            'title' => $data->title,
            'description' => $data->description,
            'severity_id' => $data->severity instanceof Optional ? $data->severity : $this->resolveId(AdvisorySeverity::class, 'severity', $data->severity, $errors),
            'status_id' => $data->status instanceof Optional ? $data->status : $this->resolveId(AdvisoryStatus::class, 'status', $data->status, $errors),
            'assigned_department_id' => is_string($data->department) ? $this->resolveId(Department::class, 'department', $data->department, $errors) : $data->department,
        ], fn (mixed $value): bool => ! $value instanceof Optional);

        $contactTypeIds = null;

        if (AdvisoryVisibilityFeature::active() && ! $data->restrictedToContactTypes instanceof Optional) {
            $contactTypeIds = $this->resolveContactTypeIds($data->restrictedToContactTypes ?? [], $errors);
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return DB::transaction(function () use ($data, $existing, $attributes, $contactTypeIds): Advisory {
            $advisory = $existing === null
                ? new Advisory()
                : Advisory::query()->whereKey($existing->getKey())->lockForUpdate()->firstOrFail();

            if (AdvisoryVisibilityFeature::active()) {
                $attributes = [
                    ...$attributes,
                    ...$this->visibilityAttributes($data, $advisory),
                ];

                $this->ensureRestrictedAdvisoryHasContactTypes($advisory, $attributes, $contactTypeIds);
            }

            $advisory->fill($attributes);
            $advisory->save();

            if ($contactTypeIds !== null) {
                $advisory->restrictedToContactTypes()->sync($contactTypeIds);
            }

            return $advisory;
        });
    }

    /**
     * Visibility attributes that were not sent keep their current values, or default to public and unrestricted when creating.
     *
     * @return array{visibility: AdvisoryVisibility, is_visibility_restricted: bool}
     */
    private function visibilityAttributes(AdvisoryDataObject $data, Advisory $advisory): array
    {
        return [
            'visibility' => $data->visibility instanceof Optional
                ? ($advisory->exists ? $advisory->visibility : AdvisoryVisibility::Public)
                : $data->visibility,
            'is_visibility_restricted' => $data->isVisibilityRestricted instanceof Optional
                ? ($advisory->exists && $advisory->is_visibility_restricted)
                : $data->isVisibilityRestricted,
        ];
    }

    /**
     * @param array{visibility: AdvisoryVisibility, is_visibility_restricted: bool} $attributes
     * @param array<int, string>|null $contactTypeIds The replacement contact types, or `null` to keep the current ones.
     *
     * @throws ValidationException
     */
    private function ensureRestrictedAdvisoryHasContactTypes(Advisory $advisory, array $attributes, ?array $contactTypeIds): void
    {
        if ($attributes['visibility'] !== AdvisoryVisibility::Authenticated || ! $attributes['is_visibility_restricted']) {
            return;
        }

        $hasContactTypes = $contactTypeIds !== null
            ? $contactTypeIds !== []
            : $advisory->exists && $advisory->restrictedToContactTypes()->exists();

        if (! $hasContactTypes) {
            throw ValidationException::withMessages([
                'restricted_to_contact_types' => 'At least one contact type is required when the advisory is restricted to specific contact types.',
            ]);
        }
    }

    /**
     * Severities, statuses, and departments are referenced by name. Severity and status names may not be unique
     * while their uniqueness flags are inactive, so an ambiguous name is reported rather than guessed.
     *
     * TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): once both flags are removed, every name is unique,
     * so look the ID up with `value('id')` and drop the `limit(2)` and the ambiguous-name error.
     *
     * @param class-string<AdvisorySeverity|AdvisoryStatus|Department> $model
     * @param array<string, string> $errors
     */
    private function resolveId(string $model, string $field, string $name, array &$errors): ?string
    {
        $ids = $model::query()->where('name', $name)->limit(2)->pluck('id');

        if ($ids->count() === 1) {
            $id = $ids->first();

            assert(is_string($id));

            return $id;
        }

        $errors[$field] = $ids->isEmpty()
            ? "The specified {$field} does not exist."
            : "The specified {$field} name is ambiguous.";

        return null;
    }

    /**
     * Contact type names are not unique, so each name must match exactly one type, ignoring case.
     *
     * @param array<int, string> $names
     * @param array<string, string> $errors
     *
     * @return array<int, string>
     */
    private function resolveContactTypeIds(array $names, array &$errors): array
    {
        $contactTypesByName = ContactType::query()
            ->whereIn(new Expression('lower(name)'), array_map(Str::lower(...), $names))
            ->get(['id', 'name'])
            ->groupBy(fn (ContactType $contactType): string => Str::lower($contactType->name));

        $ids = [];

        foreach ($names as $index => $name) {
            $contactTypes = $contactTypesByName->get(Str::lower($name));

            if ($contactTypes?->count() === 1) {
                $ids[] = $contactTypes->first()->getKey();

                continue;
            }

            $errors["restricted_to_contact_types.{$index}"] = $contactTypes === null
                ? 'The specified contact type does not exist.'
                : 'The specified contact type name is ambiguous.';
        }

        return $ids;
    }
}
