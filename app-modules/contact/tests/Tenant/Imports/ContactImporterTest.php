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

use AidingApp\Contact\Imports\ContactImporter;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @param  array<string, mixed>  $row
 */
function runContactImport(array $row, ?Import $import = null): void
{
    ContactType::factory()->create([
        'name' => 'Default',
        'is_default' => true,
    ]);

    if ($import === null) {
        $import = new Import();
        $import->id = (string) Str::uuid();
        $import->user()->associate(User::factory()->create());
    }

    $columnMap = collect(array_keys($row))
        ->mapWithKeys(fn (string $key): array => [$key => $key])
        ->all();

    $importer = new ContactImporter(
        import: $import,
        columnMap: $columnMap,
        options: [],
    );

    $importer($row);
}

it('associates an imported contact with the matching organization', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);

    runContactImport([
        'first_name' => 'First',
        'last_name' => 'Last',
        'full_name' => 'First Last',
        'type' => 'Default',
        'email' => 'person@example.com',
    ]);

    $contact = Contact::query()->where('email', 'person@example.com')->firstOrFail();

    expect($contact->organization_id)->toBe($organization->getKey());
});

it('preserves an existing organization when importing a contact', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [['domain' => 'other.com']],
    ]);
    $existingContact = Contact::factory()
        ->for($otherOrganization, 'organization')
        ->create(['email' => 'person@example.com', 'first_name' => 'Original']);

    runContactImport([
        'first_name' => 'Updated',
        'last_name' => 'Last',
        'full_name' => 'Updated Last',
        'type' => 'Default',
        'email' => 'person@example.com',
    ]);

    $contact = Contact::query()->where('email', 'person@example.com')->firstOrFail();

    expect($contact->getKey())->toBe($existingContact->getKey())
        ->and($contact->first_name)->toBe('Updated')
        ->and($contact->organization_id)->toBe($otherOrganization->getKey());
});
