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
use AidingApp\Contact\Models\Organization;
use App\Features\ContactArchivingFeature;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

$actingAsContactCreator = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['contact.view-any', 'contact.create']);
    Sanctum::actingAs($user, ['api']);

    return $user;
};

beforeEach(function (): void {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () use ($actingAsContactCreator): void {
    $payload = ['first_name' => 'Jane', 'last_name' => 'Doe', 'type' => ContactType::factory()->create()->name];

    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertUnauthorized();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('contact.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertForbidden();

    $actingAsContactCreator();
    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertCreated();
});

it('does not reveal validation details to unauthorized users', function (): void {
    $existing = Contact::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.contacts.store', [], false), ['email' => $existing->email])
        ->assertForbidden();
});

it('creates a contact with the provided attributes', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $type = ContactType::factory()->create();
    $organization = Organization::factory()->create();

    $response = postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'preferred_name' => 'JD',
        'email' => 'jane.doe@example.com',
        'type' => $type->name,
        'organization' => $organization->name,
        'postal_code' => '12345',
        'job_title' => 'Engineer',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'name', 'first_name', 'last_name', 'email', 'type' => ['id', 'name'], 'organization', 'is_managed']])
        ->assertJsonPath('data.name', 'Jane Doe')
        ->assertJsonPath('data.preferred_name', 'JD')
        ->assertJsonPath('data.postal_code', '12345')
        ->assertJsonPath('data.type.id', $type->getKey())
        ->assertJsonPath('data.organization.id', $organization->getKey());

    assertDatabaseHas(Contact::class, [
        'id' => $response['data']['id'],
        'full_name' => 'Jane Doe',
        'preferred' => 'JD',
        'postal' => '12345',
        'job_title' => 'Engineer',
        'organization_id' => $organization->getKey(),
    ]);
});

it('uses the default contact type when none is provided', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    ContactType::factory()->create(['is_default' => false]);
    $default = ContactType::factory()->create(['is_default' => true]);

    postJson(route('api.v1.contacts.store', [], false), ['first_name' => 'Jane', 'last_name' => 'Doe'])
        ->assertCreated()
        ->assertJsonPath('data.type.id', $default->getKey());
});

it('resolves relationship names case-insensitively without wildcard matching', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    ContactType::factory()->create(['name' => 'VIPXContact']);
    $type = ContactType::factory()->create(['name' => 'VIP_Contact']);
    Organization::factory()->create(['name' => '100 Percent Extra']);
    $organization = Organization::factory()->create(['name' => '100% Company']);

    $response = postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'type' => 'vip_contact',
        'organization' => '100% company',
    ])->assertCreated()
        ->assertJsonPath('data.type.id', $type->getKey())
        ->assertJsonPath('data.organization.id', $organization->getKey());

    assertDatabaseHas(Contact::class, [
        'id' => $response['data']['id'],
        'type_id' => $type->getKey(),
        'organization_id' => $organization->getKey(),
    ]);
});

it('rejects ambiguous contact type names', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    ContactType::factory()->create(['name' => 'Customer']);
    ContactType::factory()->create(['name' => 'CUSTOMER']);

    postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'type' => 'customer',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['type'])
        ->assertJsonPath('errors.type.0', 'The specified contact type name is ambiguous.');

    assertDatabaseMissing(Contact::class, ['first_name' => 'Jane']);
});

it('does not resolve deleted relationships by name', function (string $field) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $type = ContactType::factory()->create(['name' => 'Customer']);
    $organization = Organization::factory()->create(['name' => 'Acme']);

    if ($field === 'type') {
        $type->delete();
    } else {
        $organization->delete();
    }

    postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'type' => $type->name,
        'organization' => $organization->name,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);

    assertDatabaseMissing(Contact::class, ['first_name' => 'Jane']);
})->with(['type', 'organization']);

it('requires a contact type when no default is available', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    postJson(route('api.v1.contacts.store', [], false), ['first_name' => 'Jane', 'last_name' => 'Doe'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);

    assertDatabaseMissing(Contact::class, ['first_name' => 'Jane']);
});

it('validates the request', function (array $payload, array $errors) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $existing = Contact::factory()->create(['email' => 'taken@example.com']);
    Contact::factory()->create()->delete();

    $payload = array_merge(['first_name' => 'Jane', 'last_name' => 'Doe', 'type' => ContactType::factory()->create()->name], $payload);

    postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(Contact::query()->count())->toBe(1)
        ->and($existing->exists)->toBeTrue();
})->with([
    'first name is required' => [['first_name' => null], ['first_name']],
    'last name is required' => [['last_name' => null], ['last_name']],
    'email must be valid' => [['email' => 'not-an-email'], ['email']],
    'email must be unique' => [['email' => 'taken@example.com'], ['email']],
    'type must exist' => [['type' => 'Unknown type'], ['type']],
    'organization must exist' => [['organization' => 'Unknown organization'], ['organization']],
    'type must be a string' => [['type' => ['Invalid']], ['type']],
    'organization must be a string' => [['organization' => ['Invalid']], ['organization']],
    'type UUID field is not accepted' => [['type_id' => '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11'], ['type_id']],
    'organization UUID field is not accepted' => [['organization_id' => '2f7b2f5e-7a43-4a0c-8d3f-4d2f9a3b7c11'], ['organization_id']],
    'attributes are limited to 255 characters' => [['job_title' => str_repeat('a', 256)], ['job_title']],
]);

it('allows an email address belonging to a deleted contact', function () use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    Contact::factory()->create(['email' => 'reused@example.com'])->delete();

    postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'reused@example.com',
        'type' => ContactType::factory()->create()->name,
    ])->assertCreated();
});

describe('archived contacts', function () use ($actingAsContactCreator): void {
    it('unarchives an archived contact with the same email and replaces its data', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        $organization = Organization::factory()->create();
        $archived = Contact::factory()->archived()->for($organization, 'organization')->create([
            'email' => 'Reused@Example.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
            'full_name' => 'Old Name',
            'phone' => '555-0100',
            'job_title' => 'Old title',
        ]);
        $type = ContactType::factory()->create(['name' => 'Restored Type']);

        expect($archived->refresh()->isArchived())->toBeTrue();

        $response = postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'reused@example.com',
            'type' => $type->name,
            'organization' => null,
            'job_title' => 'New title',
        ])->assertCreated()
            ->assertJsonPath('data.id', $archived->getKey())
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.job_title', 'New title')
            ->assertJsonPath('data.phone', null)
            ->assertJsonPath('data.organization', null)
            ->assertJsonPath('data.type.id', $type->getKey());

        $archived->refresh();

        expect($archived->isArchived())->toBeFalse()
            ->and($archived->full_name)->toBe('Jane Doe')
            ->and($archived->phone)->toBeNull()
            ->and($archived->organization_id)->toBeNull()
            ->and($archived->type_id)->toBe($type->getKey())
            ->and(Contact::query()->count())->toBe(1)
            ->and($response['data']['id'])->toBe($archived->getKey());
    });

    it('matches the restored contact to an organization when none is provided', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        $organization = Organization::factory()->create(['domains' => [['domain' => 'example.com']]]);
        $archived = Contact::factory()->archived()->create(['email' => 'jane@example.com', 'organization_id' => null]);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
        ])->assertCreated()
            ->assertJsonPath('data.id', $archived->getKey())
            ->assertJsonPath('data.organization.id', $organization->getKey());
    });

    it('does not match the restored contact to an organization when it is explicitly cleared', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        Organization::factory()->create(['domains' => [['domain' => 'example.com']]]);
        Contact::factory()->archived()->create(['email' => 'jane@example.com', 'organization_id' => null]);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
            'organization' => null,
        ])->assertCreated()
            ->assertJsonPath('data.organization', null);
    });

    it('rolls back the overwrite when restoring the archived contact is cancelled', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        $archived = Contact::factory()->archived()->create([
            'email' => 'archived@example.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
            'full_name' => 'Old Name',
            'phone' => '555-0100',
        ]);
        Contact::unarchiving(fn (): bool => false);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'archived@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', 'The archived contact with this email could not be restored.');

        $archived->refresh();

        expect($archived->isArchived())->toBeTrue()
            ->and($archived->full_name)->toBe('Old Name')
            ->and($archived->phone)->toBe('555-0100')
            ->and(Contact::query()->count())->toBe(1);
    });

    it('still rejects an email used by an active contact', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        Contact::factory()->create(['email' => 'taken@example.com']);
        $archived = Contact::factory()->archived()->create(['email' => 'archived@example.com']);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'taken@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        expect($archived->refresh()->isArchived())->toBeTrue();
    });

    it('does not restore an archived contact that uses a different email', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        $archived = Contact::factory()->archived()->create(['email' => 'archived@example.com']);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'new@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
        ])->assertCreated()
            ->assertJsonPath('data.id', fn (string $id): bool => $id !== $archived->getKey());

        expect($archived->refresh()->isArchived())->toBeTrue()
            ->and(Contact::query()->count())->toBe(2);
    });

    it('does not restore an archived contact while archiving is inactive', function () use ($actingAsContactCreator): void {
        $actingAsContactCreator();

        ContactArchivingFeature::deactivate();

        $archived = Contact::factory()->archived()->create(['email' => 'archived@example.com']);

        postJson(route('api.v1.contacts.store', [], false), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'archived@example.com',
            'type' => ContactType::factory()->create(['name' => 'Restored Type'])->name,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        expect($archived->refresh()->isArchived())->toBeTrue();
    });
});

it('matches an organization only when the organization field is omitted', function (bool $omitOrganization) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $organization = Organization::factory()->create(['domains' => [['domain' => 'example.com']]]);
    $payload = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'type' => ContactType::factory()->create()->name,
    ];

    if (! $omitOrganization) {
        $payload['organization'] = null;
    }

    $response = postJson(route('api.v1.contacts.store', [], false), $payload)
        ->assertCreated()
        ->assertJsonPath('data.organization', $omitOrganization ? [
            'id' => $organization->getKey(),
            'name' => $organization->name,
        ] : null);

    assertDatabaseHas(Contact::class, [
        'id' => $response['data']['id'],
        'organization_id' => $omitOrganization ? $organization->getKey() : null,
    ]);
})->with([
    'omitted organization' => [true],
    'explicitly null organization' => [false],
]);

it('creates a contact at the combined name length boundary', function (int $firstNameLength) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    $firstName = str_repeat("\u{00E9}", $firstNameLength);
    $lastName = str_repeat('b', 127);

    $response = postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'type' => ContactType::factory()->create()->name,
    ])->assertCreated()
        ->assertJsonPath('data.name', "{$firstName} {$lastName}");

    assertDatabaseHas(Contact::class, [
        'id' => $response['data']['id'],
        'full_name' => "{$firstName} {$lastName}",
    ]);
})->with([
    '254 characters' => [126],
    '255 characters' => [127],
]);

it('rejects a combined name longer than the column allows', function (int $lastNameLength) use ($actingAsContactCreator): void {
    $actingAsContactCreator();

    postJson(route('api.v1.contacts.store', [], false), [
        'first_name' => str_repeat('a', 128),
        'last_name' => str_repeat('b', $lastNameLength),
        'type' => ContactType::factory()->create()->name,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name', 'last_name'])
        ->assertJsonPath('errors.first_name.0', 'The full name may not be greater than 255 characters.')
        ->assertJsonPath('errors.last_name.0', 'The full name may not be greater than 255 characters.');

    expect(Contact::query()->count())->toBe(0);
})->with([
    '256 characters' => [127],
    '257 characters' => [128],
]);
