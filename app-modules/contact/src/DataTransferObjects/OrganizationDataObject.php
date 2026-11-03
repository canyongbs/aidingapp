<?php

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
