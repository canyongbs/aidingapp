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

namespace AidingApp\ServiceManagement\DataTransferObjects;

use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapOutputName(SnakeCaseMapper::class)]
class AdvisoryDataObject extends Data
{
    /**
     * @param string|Optional $severity The name of the advisory severity.
     * @param string|Optional $status The name of the advisory status.
     * @param string|null|Optional $department The name of the assigned department.
     * @param array<int, string>|null|Optional $restrictedToContactTypes The names of the contact types.
     */
    public function __construct(
        public string | Optional $title,
        public string | Optional $description,
        public string | Optional $severity,
        public string | Optional $status,
        public string | null | Optional $department,
        public AdvisoryVisibility | Optional $visibility,
        public bool | Optional $isVisibilityRestricted,
        public array | null | Optional $restrictedToContactTypes,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        $visibility = self::value($data, 'visibility');
        $isVisibilityRestricted = self::value($data, 'is_visibility_restricted');

        return new self(
            title: self::value($data, 'title'),
            description: self::value($data, 'description'),
            severity: self::value($data, 'severity'),
            status: self::value($data, 'status'),
            department: self::value($data, 'department'),
            visibility: is_string($visibility) ? AdvisoryVisibility::from($visibility) : $visibility,
            // The `boolean` rule also accepts `0`, `1`, `'0'`, and `'1'`.
            isVisibilityRestricted: $isVisibilityRestricted instanceof Optional ? $isVisibilityRestricted : (bool) $isVisibilityRestricted,
            restrictedToContactTypes: self::value($data, 'restricted_to_contact_types'),
        );
    }

    /**
     * Keys that were not sent are left as `Optional`, whereas keys explicitly sent as `null` are preserved.
     *
     * @param array<string, mixed> $data
     */
    protected static function value(array $data, string $key): mixed
    {
        return array_key_exists($key, $data) ? $data[$key] : Optional::create();
    }
}
