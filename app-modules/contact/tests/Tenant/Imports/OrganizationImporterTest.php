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

use AidingApp\Contact\Imports\OrganizationImporter;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Cache::flush();
});

/**
 * @param  array<string, mixed>  $row
 */
function runOrganizationImport(array $row, ?Import $import = null): void
{
    $columnMap = collect(array_keys($row))
        ->mapWithKeys(fn (string $key): array => [$key => $key])
        ->all();

    $importer = new OrganizationImporter(
        import: $import ?? new Import(),
        columnMap: $columnMap,
        options: [],
    );

    $importer($row);
}

it('creates a new organization with all fields, industry and type', function () {
    $industry = OrganizationIndustry::factory()->create(['name' => 'Technology']);
    $type = OrganizationType::factory()->create(['name' => 'Partner']);

    runOrganizationImport([
        'name' => 'Acme Corporation',
        'email' => 'info@acme.com',
        'phone_number' => '+1 (555) 555-5555',
        'website' => 'https://www.acme.com',
        'industry' => 'Technology',
        'type' => 'Partner',
        'description' => 'A description of the organization.',
        'number_of_employees' => '250',
        'address' => '123 Main St.',
        'city' => 'Springfield',
        'state' => 'IL',
        'postalcode' => '62704',
        'country' => 'United States',
        'linkedin_url' => 'https://www.linkedin.com/company/acme',
        'facebook_url' => 'https://www.facebook.com/acme',
        'twitter_url' => 'https://twitter.com/acme',
        'is_contact_generation_enabled' => 'true',
    ]);

    $organization = Organization::query()->where('name', 'Acme Corporation')->firstOrFail();

    expect($organization->email)->toBe('info@acme.com')
        ->and($organization->website)->toBe('https://www.acme.com')
        ->and($organization->industry->is($industry))->toBeTrue()
        ->and($organization->type->is($type))->toBeTrue()
        ->and($organization->number_of_employees)->toBe(250)
        ->and($organization->city)->toBe('Springfield')
        ->and($organization->is_contact_generation_enabled)->toBeTrue();
});

it('updates an existing organization matched by name case-insensitively', function () {
    $existing = Organization::factory()->create([
        'name' => 'Globex',
        'city' => 'Old City',
    ]);

    runOrganizationImport([
        'name' => 'GLOBEX',
        'city' => 'New City',
    ]);

    expect(Organization::query()->where('name', 'Globex')->count())->toBe(1);

    expect($existing->refresh()->city)->toBe('New City');
});

it('resolves industry and type by name', function () {
    $industry = OrganizationIndustry::factory()->create(['name' => 'Finance']);
    $type = OrganizationType::factory()->create(['name' => 'Vendor']);

    runOrganizationImport([
        'name' => 'Initech',
        'industry' => 'Finance',
        'type' => 'Vendor',
    ]);

    $organization = Organization::query()->where('name', 'Initech')->firstOrFail();

    expect($organization->industry->is($industry))->toBeTrue()
        ->and($organization->type->is($type))->toBeTrue();
});

it('resolves industry and type by name case-insensitively', function () {
    OrganizationIndustry::factory()->create(['name' => 'Default Industry', 'is_default' => true]);
    OrganizationType::factory()->create(['name' => 'Default Type', 'is_default' => true]);

    $industry = OrganizationIndustry::factory()->create(['name' => 'Finance']);
    $type = OrganizationType::factory()->create(['name' => 'Vendor']);

    runOrganizationImport([
        'name' => 'Initech',
        'industry' => 'finance',
        'type' => 'VENDOR',
    ]);

    $organization = Organization::query()->where('name', 'Initech')->firstOrFail();

    expect($organization->industry->is($industry))->toBeTrue()
        ->and($organization->type->is($type))->toBeTrue();
});

it('falls back to the default industry and type when the column is blank', function () {
    $defaultIndustry = OrganizationIndustry::factory()->create(['name' => 'Default Industry', 'is_default' => true]);
    $defaultType = OrganizationType::factory()->create(['name' => 'Default Type', 'is_default' => true]);

    runOrganizationImport([
        'name' => 'Umbrella',
        'industry' => '',
        'type' => '',
    ]);

    $organization = Organization::query()->where('name', 'Umbrella')->firstOrFail();

    expect($organization->industry->is($defaultIndustry))->toBeTrue()
        ->and($organization->type->is($defaultType))->toBeTrue();
});

it('fails the row when the name is missing', function () {
    runOrganizationImport([
        'name' => '',
        'city' => 'Nowhere',
    ]);
})->throws(ValidationException::class);

it('imports domains from a pipe-separated list', function () {
    runOrganizationImport([
        'name' => 'College Community College',
        'domains' => 'collegecc.edu|college.edu',
    ]);

    $organization = Organization::query()->where('name', 'College Community College')->firstOrFail();

    expect($organization->domains)->toBe([
        ['domain' => 'collegecc.edu'],
        ['domain' => 'college.edu'],
    ]);
});

it('imports domains from a comma-separated list', function () {
    runOrganizationImport([
        'name' => 'College Community College',
        'domains' => 'collegecc.edu, college.edu ',
    ]);

    $organization = Organization::query()->where('name', 'College Community College')->firstOrFail();

    expect($organization->domains)->toBe([
        ['domain' => 'collegecc.edu'],
        ['domain' => 'college.edu'],
    ]);
});

it('fails the row when a domain is invalid', function () {
    runOrganizationImport([
        'name' => 'College Community College',
        'domains' => 'collegecc.edu|not a domain',
    ]);
})->throws(ValidationException::class);

it('fails the row when the same domain is listed more than once in one row', function () {
    runOrganizationImport([
        'name' => 'College Community College',
        'domains' => 'collegecc.edu|CollegeCC.edu',
    ]);
})->throws(ValidationException::class);

it('rejects a domain already used by another organization', function () {
    Organization::factory()->create([
        'name' => 'Existing College',
        'domains' => [['domain' => 'shared.edu']],
    ]);

    runOrganizationImport([
        'name' => 'New College',
        'domains' => 'shared.edu',
    ]);
})->throws(RowImportFailedException::class);

it('allows an organization to keep its own domain while adding another on update', function () {
    $organization = Organization::factory()->create([
        'name' => 'Keep College',
        'domains' => [['domain' => 'keep.edu']],
    ]);

    runOrganizationImport([
        'name' => 'Keep College',
        'domains' => 'keep.edu|extra.edu',
    ]);

    expect($organization->refresh()->domains)->toBe([
        ['domain' => 'keep.edu'],
        ['domain' => 'extra.edu'],
    ]);
});

it('rejects a domain already claimed earlier in the same import', function () {
    $import = new Import();
    $import->id = (string) Str::uuid();

    Cache::tags([OrganizationImporter::domainClaimCacheTag($import->getKey())])
        ->add('shared.edu', true, now()->addDay());

    runOrganizationImport([
        'name' => 'New College',
        'domains' => 'Shared.edu',
    ], $import);
})->throws(RowImportFailedException::class);

it('does not reject a domain claimed by a different import', function () {
    $otherImport = new Import();
    $otherImport->id = (string) Str::uuid();

    Cache::tags([OrganizationImporter::domainClaimCacheTag($otherImport->getKey())])
        ->add('shared.edu', true, now()->addDay());

    $import = new Import();
    $import->id = (string) Str::uuid();

    runOrganizationImport([
        'name' => 'New College',
        'domains' => 'shared.edu',
    ], $import);

    expect(Organization::query()->where('name', 'New College')->firstOrFail()->domains)
        ->toBe([['domain' => 'shared.edu']]);
});
