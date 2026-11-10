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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Services\ManagedContactService;
use App\Features\ContactArchivingFeature;
use App\Models\SystemUser;
use App\Models\User;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Tests\actingAsApiUser;

$actingAsContactDeleter = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.*.delete']);
    actingAsApiUser($user);

    return $user;
};

it('archives a contact without deleting it and revokes its tokens', function () use ($actingAsContactDeleter): void {
    $actingAsContactDeleter();

    $contact = Contact::factory()->create();
    $contact->createToken('portal');

    expect($contact->isArchived())->toBeFalse()
        ->and($contact->tokens()->count())->toBe(1);

    deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
        ->assertNoContent();

    $contact->refresh();

    assertModelExists($contact);
    expect($contact->isArchived())->toBeTrue()
        ->and($contact->deleted_at)->toBeNull()
        ->and($contact->tokens()->count())->toBe(0);
});

it('does not find an already archived contact', function () use ($actingAsContactDeleter): void {
    $actingAsContactDeleter();

    $contact = Contact::factory()->archived()->create();
    $archivedAt = $contact->getRawOriginal('archived_at');

    deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
        ->assertNotFound();

    expect($contact->refresh()->getRawOriginal('archived_at'))->toBe($archivedAt);
});

it('checks permissions before returning not found for an archived contact', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    actingAsApiUser($user);

    $contact = Contact::factory()->archived()->create();

    deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
        ->assertForbidden();
});

it('does not report success when archiving is cancelled', function () use ($actingAsContactDeleter): void {
    $actingAsContactDeleter();

    $contact = Contact::factory()->create();
    Contact::archiving(fn (): bool => false);

    deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['contact'])
        ->assertJsonPath('errors.contact.0', 'The contact could not be archived.');

    expect($contact->refresh()->isArchived())->toBeFalse();
});

it('soft deletes the contact when contact archiving is inactive', function () use ($actingAsContactDeleter): void {
    $actingAsContactDeleter();

    ContactArchivingFeature::deactivate();
    $contact = Contact::factory()->create();

    deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
        ->assertNoContent();

    assertSoftDeleted($contact);

    expect($contact->refresh()->isArchived())->toBeFalse();
});

it('does not archive missing or deleted contacts', function (bool $deleted) use ($actingAsContactDeleter): void {
    $actingAsContactDeleter();

    $id = '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11';

    if ($deleted) {
        $contact = Contact::factory()->create();
        $contact->delete();
        $id = $contact->getKey();
    }

    deleteJson(route('api.v1.contacts.delete', ['contact' => $id], false))
        ->assertNotFound();
})->with([
    'missing' => [false],
    'deleted' => [true],
]);

describe('authorization', function () use ($actingAsContactDeleter): void {
    it('requires authentication', function (): void {
        $contact = Contact::factory()->create();

        deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
            ->assertUnauthorized();

        expect($contact->refresh()->isArchived())->toBeFalse();
    });

    it('requires both view-any and delete permissions', function (array $permissions): void {
        $user = SystemUser::factory()->create();
        $user->givePermissionTo($permissions);
        actingAsApiUser($user);

        $contact = Contact::factory()->create();

        deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
            ->assertForbidden();

        expect($contact->refresh()->isArchived())->toBeFalse();
    })->with([
        'no permissions' => [[]],
        'view-any only' => [['contact.view-any']],
        'delete only' => [['contact.*.delete']],
    ]);

    it('does not archive managed contacts', function () use ($actingAsContactDeleter): void {
        $actingAsContactDeleter();

        $contact = app(ManagedContactService::class)->enable(User::factory()->create(), ContactType::factory()->create()->getKey());

        deleteJson(route('api.v1.contacts.delete', ['contact' => $contact], false))
            ->assertForbidden();

        expect($contact->refresh()->isArchived())->toBeFalse();
    });
});
