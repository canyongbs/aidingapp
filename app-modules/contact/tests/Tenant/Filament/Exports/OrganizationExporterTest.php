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

use AidingApp\Contact\Filament\Exports\OrganizationExporter;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use Filament\Actions\Exports\Models\Export;

/**
 * @return array<int, mixed>
 */
function exportOrganizationRow(Organization $organization): array
{
    $columnMap = [
        'name' => 'Name',
        'email' => 'Email',
        'phone_number' => 'Phone Number',
        'website' => 'Website',
        'industry.name' => 'Industry',
        'type.name' => 'Type',
        'description' => 'Description',
        'number_of_employees' => 'Number of Employees',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State',
        'postalcode' => 'Postal Code',
        'country' => 'Country',
        'linkedin_url' => 'LinkedIn URL',
        'facebook_url' => 'Facebook URL',
        'twitter_url' => 'Twitter URL',
        'domains' => 'Domains',
        'is_contact_generation_enabled' => 'Automatically generate contact record on login',
    ];

    $exporter = new OrganizationExporter(
        export: new Export(),
        columnMap: $columnMap,
        options: [],
    );

    return $exporter($organization);
}

it('exports all organization fields, the industry and type names, and domains joined with a pipe', function () {
    $industry = OrganizationIndustry::factory()->create(['name' => 'Technology']);
    $type = OrganizationType::factory()->create(['name' => 'Partner']);

    $organization = Organization::factory()
        ->for($industry, 'industry')
        ->for($type, 'type')
        ->create([
            'name' => 'Acme Corporation',
            'email' => 'info@acme.com',
            'website' => 'https://www.acme.com',
            'city' => 'Springfield',
            'domains' => [
                ['domain' => 'acme.com'],
                ['domain' => 'acme.org'],
            ],
            'is_contact_generation_enabled' => true,
        ]);

    $row = exportOrganizationRow($organization);

    expect($row[0])->toBe('Acme Corporation')
        ->and($row[1])->toBe('info@acme.com')
        ->and($row[3])->toBe('https://www.acme.com')
        ->and($row[4])->toBe('Technology')
        ->and($row[5])->toBe('Partner')
        ->and($row[9])->toBe('Springfield')
        ->and($row[16])->toBe('acme.com|acme.org')
        ->and($row[17])->toBe('true');
});

it('exports a blank domains value when the organization has no domains', function () {
    $organization = Organization::factory()->create(['domains' => []]);

    expect(blank(exportOrganizationRow($organization)[16]))->toBeTrue();
});

it('exports a blank industry and type when the organization has none', function () {
    $organization = Organization::factory()->create([
        'industry_id' => null,
        'type_id' => null,
    ]);

    $row = exportOrganizationRow($organization);

    expect(blank($row[4]))->toBeTrue()
        ->and(blank($row[5]))->toBeTrue();
});

it('exports is_contact_generation_enabled as false when disabled', function () {
    $organization = Organization::factory()->create(['is_contact_generation_enabled' => false]);

    expect(exportOrganizationRow($organization)[17])->toBe('false');
});
