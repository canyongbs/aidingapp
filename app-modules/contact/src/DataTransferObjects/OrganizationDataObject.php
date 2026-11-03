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
class OrganizationDataObject extends Data
{
    /**
     * @param array<int, string>|null|Optional $domains
     */
    public function __construct(
        public string | Optional $name,
        public string | null | Optional $email,
        public string | null | Optional $phoneNumber,
        public string | null | Optional $website,
        public string | null | Optional $industryId,
        public string | null | Optional $typeId,
        public string | null | Optional $description,
        public int | null | Optional $numberOfEmployees,
        public string | null | Optional $address,
        public string | null | Optional $city,
        public string | null | Optional $state,
        public string | null | Optional $postalcode,
        public string | null | Optional $country,
        public string | null | Optional $linkedinUrl,
        public string | null | Optional $facebookUrl,
        public string | null | Optional $twitterUrl,
        public array | null | Optional $domains,
        public bool | Optional $isContactGenerationEnabled,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        return new self(
            name: self::value($data, 'name'),
            email: self::value($data, 'email'),
            phoneNumber: self::value($data, 'phone_number'),
            website: self::value($data, 'website'),
            industryId: self::value($data, 'industry_id'),
            typeId: self::value($data, 'type_id'),
            description: self::value($data, 'description'),
            numberOfEmployees: self::value($data, 'number_of_employees'),
            address: self::value($data, 'address'),
            city: self::value($data, 'city'),
            state: self::value($data, 'state'),
            postalcode: self::value($data, 'postalcode'),
            country: self::value($data, 'country'),
            linkedinUrl: self::value($data, 'linkedin_url'),
            facebookUrl: self::value($data, 'facebook_url'),
            twitterUrl: self::value($data, 'twitter_url'),
            domains: self::value($data, 'domains'),
            isContactGenerationEnabled: self::value($data, 'is_contact_generation_enabled'),
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
