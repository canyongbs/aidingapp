<?php

namespace AidingApp\Contact\Actions;

use AidingApp\Contact\DataTransferObjects\OrganizationDataObject;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use Spatie\LaravelData\Optional;

class SaveOrganization
{
    /**
     * Creates the given organization, or updates it when an existing one is provided.
     * Only attributes present on the data object are written.
     */
    public function __invoke(OrganizationDataObject $data, ?Organization $organization = null): Organization
    {
        $attributes = $data->toArray();

        if ($data->domains instanceof Optional) {
            unset($attributes['domains']);
        } else {
            $attributes['domains'] = filled($data->domains)
                ? array_map(fn (string $domain): array => ['domain' => $domain], array_values($data->domains))
                : null;
        }

        if ($organization === null) {
            $attributes = [
                'industry_id' => OrganizationIndustry::query()->where('is_default', true)->value('id'),
                'type_id' => OrganizationType::query()->where('is_default', true)->value('id'),
                ...$attributes,
            ];

            $organization = new Organization();
        }

        $organization->fill($attributes)->save();

        return $organization;
    }
}
