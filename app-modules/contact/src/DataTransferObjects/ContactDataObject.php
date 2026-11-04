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

namespace AidingApp\Contact\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapOutputName(SnakeCaseMapper::class)]
class ContactDataObject extends Data
{
    /**
     * Properties that map to contact columns are output under the column name.
     *
     * @param string|null|Optional $type The name of the contact type.
     * @param string|null|Optional $organization The name of the organization.
     */
    public function __construct(
        public string | Optional $firstName,
        public string | Optional $lastName,
        #[MapOutputName('preferred')]
        public string | null | Optional $preferredName,
        public string | null | Optional $email,
        public string | null | Optional $mobile,
        public string | null | Optional $phone,
        public string | null | Optional $type,
        public string | null | Optional $organization,
        public string | null | Optional $jobTitle,
        public string | null | Optional $employeeId,
        public string | null | Optional $workNumber,
        public string | null | Optional $workExtension,
        public string | null | Optional $studentId,
        public string | null | Optional $school,
        public string | null | Optional $academicDepartment,
        public string | null | Optional $program,
        public string | null | Optional $address,
        #[MapOutputName('address_2')]
        public string | null | Optional $address2,
        public string | null | Optional $city,
        public string | null | Optional $state,
        #[MapOutputName('postal')]
        public string | null | Optional $postalCode,
        public string | null | Optional $country,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        return new self(
            firstName: self::value($data, 'first_name'),
            lastName: self::value($data, 'last_name'),
            preferredName: self::value($data, 'preferred_name'),
            email: self::value($data, 'email'),
            mobile: self::value($data, 'mobile'),
            phone: self::value($data, 'phone'),
            type: self::value($data, 'type'),
            organization: self::value($data, 'organization'),
            jobTitle: self::value($data, 'job_title'),
            employeeId: self::value($data, 'employee_id'),
            workNumber: self::value($data, 'work_number'),
            workExtension: self::value($data, 'work_extension'),
            studentId: self::value($data, 'student_id'),
            school: self::value($data, 'school'),
            academicDepartment: self::value($data, 'academic_department'),
            program: self::value($data, 'program'),
            address: self::value($data, 'address'),
            address2: self::value($data, 'address_2'),
            city: self::value($data, 'city'),
            state: self::value($data, 'state'),
            postalCode: self::value($data, 'postal_code'),
            country: self::value($data, 'country'),
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
