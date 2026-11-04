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

use AidingApp\Contact\Actions\UpdateContact;
use AidingApp\Contact\Models\Contact;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

// The stale model stands in for the route-bound snapshot that another request has since changed.
it('derives the full name from the current row so concurrent partial updates stay consistent', function (): void {
    $contact = Contact::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'full_name' => 'Jane Doe']);
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    Contact::query()->whereKey($contact->getKey())->update(['first_name' => 'Janet', 'full_name' => 'Janet Doe']);

    app(UpdateContact::class)($staleSnapshot, ['last_name' => 'Smith'], null, null, false);

    $contact->refresh();

    expect($contact->first_name)->toBe('Janet')
        ->and($contact->last_name)->toBe('Smith')
        ->and($contact->full_name)->toBe('Janet Smith');
});

it('validates the combined name length against the current row', function (): void {
    $contact = Contact::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'full_name' => 'Jane Doe']);
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    Contact::query()->whereKey($contact->getKey())->update(['first_name' => str_repeat('a', 200), 'full_name' => str_repeat('a', 200) . ' Doe']);

    expect(fn () => app(UpdateContact::class)($staleSnapshot, ['last_name' => str_repeat('b', 100)], null, null, false))
        ->toThrow(ValidationException::class);

    expect($contact->refresh()->last_name)->toBe('Doe')
        ->and($contact->full_name)->toBe(str_repeat('a', 200) . ' Doe');
});

it('does not update a contact that was archived after it was loaded', function (): void {
    $contact = Contact::factory()->create();
    $staleSnapshot = Contact::query()->findOrFail($contact->getKey());

    $contact->archive();

    expect(fn () => app(UpdateContact::class)($staleSnapshot, ['job_title' => 'Updated'], null, null, false))
        ->toThrow(ModelNotFoundException::class);

    expect($contact->refresh()->job_title)->not->toBe('Updated');
});
