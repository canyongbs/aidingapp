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

namespace App\DataTransferObjects;

use AidingApp\Authorization\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateUserDataObject extends Data
{
    /**
     * @param Collection<int, Role>|Optional $roles
     */
    public function __construct(
        public string $name,
        public string $email,
        public bool $isExternal,
        public string | Optional $jobTitle,
        public string | Optional $phoneNumber,
        public string | Optional $workNumber,
        public int | Optional $workExtension,
        public string | Optional $mobile,
        public string | Optional $departmentId,
        public Collection | Optional $roles,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            isExternal: $data['is_external'],
            jobTitle: $data['job_title'] ?? Optional::create(),
            phoneNumber: $data['phone_number'] ?? Optional::create(),
            workNumber: $data['work_number'] ?? Optional::create(),
            workExtension: $data['work_extension'] ?? Optional::create(),
            mobile: $data['mobile'] ?? Optional::create(),
            departmentId: $data['department_id'] ?? Optional::create(),
            roles: $data['roles'] ?? Optional::create(),
        );
    }
}
