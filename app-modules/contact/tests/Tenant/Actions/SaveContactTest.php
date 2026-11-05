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

use AidingApp\Contact\Actions\SaveContact;
use AidingApp\Contact\DataTransferObjects\ContactDataObject;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

it('maps the data object to contact columns, leaving unsent attributes out', function (): void {
    $data = ContactDataObject::fromData([
        'first_name' => 'Jane',
        'preferred_name' => 'JD',
        'address_2' => 'Suite 5',
        'postal_code' => '12345',
        'job_title' => null,
    ]);

    expect($data->toArray())->toEqual([
        'first_name' => 'Jane',
        'preferred' => 'JD',
        'address_2' => 'Suite 5',
        'postal' => '12345',
        'job_title' => null,
    ]);
});

it('creates a contact from a data object with the type and organization resolved by name', function (): void {
    $type = ContactType::factory()->create(['name' => 'Alumni']);
    $organization = Organization::factory()->create(['name' => 'Acme University']);

    $contact = app(SaveContact::class)(ContactDataObject::fromData([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'preferred_name' => 'JD',
        'type' => 'alumni',
        'organization' => 'ACME UNIVERSITY',
        'postal_code' => '12345',
    ]));

    $contact->refresh();

    expect($contact->full_name)->toBe('Jane Doe')
        ->and($contact->preferred)->toBe('JD')
        ->and($contact->postal)->toBe('12345')
        ->and($contact->type_id)->toBe($type->getKey())
        ->and($contact->organization_id)->toBe($organization->getKey());
});

it('uses the default contact type when creating without one', function (): void {
    ContactType::factory()->create(['is_default' => false]);
    $default = ContactType::factory()->create(['is_default' => true]);

    $contact = app(SaveContact::class)(ContactDataObject::fromData(['first_name' => 'Jane', 'last_name' => 'Doe']));

    expect($contact->type_id)->toBe($default->getKey());
});

it('reports every unknown or ambiguous relationship name together', function (): void {
    ContactType::factory()->create(['name' => 'Customer']);
    ContactType::factory()->create(['name' => 'CUSTOMER']);

    $exception = null;

    try {
        app(SaveContact::class)(ContactDataObject::fromData([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'type' => 'customer',
            'organization' => 'Unknown organization',
        ]));
    } catch (ValidationException $caught) {
        $exception = $caught;
    }

    expect($exception)->toBeInstanceOf(ValidationException::class)
        ->and($exception?->errors())->toBe([
            'type' => ['The specified contact type name is ambiguous.'],
            'organization' => ['The specified organization does not exist.'],
        ])
        ->and(Contact::query()->count())->toBe(0);
});

it('only writes the attributes that are present when updating', function (): void {
    $contact = Contact::factory()->create(['job_title' => 'Original', 'city' => 'Springfield', 'preferred' => 'JD']);

    app(SaveContact::class)(ContactDataObject::fromData(['job_title' => 'Updated', 'preferred_name' => null]), $contact);

    $contact->refresh();

    expect($contact->job_title)->toBe('Updated')
        ->and($contact->preferred)->toBeNull()
        ->and($contact->city)->toBe('Springfield');
});

it('leaves the type and organization alone when they are not provided on update', function (): void {
    $organization = Organization::factory()->create(['domains' => []]);
    $contact = Contact::factory()->for($organization, 'organization')->create();
    $typeId = $contact->type_id;

    app(SaveContact::class)(ContactDataObject::fromData(['job_title' => 'Updated']), $contact);

    expect($contact->refresh()->type_id)->toBe($typeId)
        ->and($contact->organization_id)->toBe($organization->getKey());
});

// The stale model stands in for the route-bound snapshot that another request has since changed.
it('derives the full name from the current row so concurrent partial updates stay consistent', function (): void {
    $contact = Contact::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'full_name' => 'Jane Doe']);
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    Contact::query()->whereKey($contact->getKey())->update(['first_name' => 'Janet', 'full_name' => 'Janet Doe']);

    app(SaveContact::class)(ContactDataObject::fromData(['last_name' => 'Smith']), $staleSnapshot);

    $contact->refresh();

    expect($contact->first_name)->toBe('Janet')
        ->and($contact->last_name)->toBe('Smith')
        ->and($contact->full_name)->toBe('Janet Smith');
});

it('validates the combined name length against the current row', function (): void {
    $contact = Contact::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'full_name' => 'Jane Doe']);
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    Contact::query()->whereKey($contact->getKey())->update(['first_name' => str_repeat('a', 200), 'full_name' => str_repeat('a', 200) . ' Doe']);

    expect(fn () => app(SaveContact::class)(ContactDataObject::fromData(['last_name' => str_repeat('b', 100)]), $staleSnapshot))
        ->toThrow(ValidationException::class);

    expect($contact->refresh()->last_name)->toBe('Doe')
        ->and($contact->full_name)->toBe(str_repeat('a', 200) . ' Doe');
});

it('does not update a contact that was archived after it was loaded', function (): void {
    $contact = Contact::factory()->create();
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    $contact->archive();

    expect(fn () => app(SaveContact::class)(ContactDataObject::fromData(['job_title' => 'Updated']), $staleSnapshot))
        ->toThrow(ModelNotFoundException::class);

    expect($contact->refresh()->job_title)->not->toBe('Updated');
});

it('reports an email claimed after validation as taken', function (bool $updating): void {
    Contact::factory()->create(['email' => 'jane@example.com']);
    $existing = $updating ? Contact::factory()->create(['email' => 'other@example.com']) : null;

    $exception = null;

    try {
        app(SaveContact::class)(ContactDataObject::fromData([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'JANE@example.com',
        ]), $existing);
    } catch (ValidationException $caught) {
        $exception = $caught;
    }

    expect($exception?->errors())->toBe(['email' => ['The email has already been taken.']])
        ->and(Contact::query()->where('email', 'jane@example.com')->count())->toBe(1);
})->with([
    'creating' => [false],
    'updating' => [true],
]);
