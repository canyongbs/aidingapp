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

use AidingApp\Contact\Actions\MatchContactToOrganization;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;

it('associates an unaffiliated contact with the matching organization', function () {
    $organization = Organization::factory()->create([
        'domains' => [],
    ]);
    $contact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);

    $organization->domains = [['domain' => 'example.com']];
    $organization->saveQuietly();

    expect($contact->refresh()->organization_id)->toBeNull();

    app(MatchContactToOrganization::class)($contact->refresh());

    expect($contact->refresh()->organization_id)->toBe($organization->getKey());
});

it('does not associate a contact with an invalid email address', function () {
    Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);
    $contact = Contact::factory()->create([
        'email' => 'invalid-email',
        'organization_id' => null,
    ]);

    app(MatchContactToOrganization::class)($contact->refresh());

    expect($contact->refresh()->organization_id)->toBeNull();
});

it('does not associate a contact when no organization domain matches', function () {
    Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);
    $contact = Contact::factory()->create([
        'email' => 'person@other.com',
        'organization_id' => null,
    ]);

    app(MatchContactToOrganization::class)($contact->refresh());

    expect($contact->refresh()->organization_id)->toBeNull();
});

it('preserves a contact organization that was already assigned', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [['domain' => 'other.com']],
    ]);
    $contact = Contact::factory()
        ->for($otherOrganization, 'organization')
        ->create(['email' => 'person@example.com']);

    app(MatchContactToOrganization::class)($contact->refresh());

    expect($contact->refresh()->organization_id)->toBe($otherOrganization->getKey());
});

it('preserves an organization assigned after a stale contact was loaded', function () {
    $organization = Organization::factory()->create([
        'domains' => [],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [['domain' => 'other.com']],
    ]);
    $contact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $staleContact = $contact->fresh();
    assert($staleContact instanceof Contact);

    $organization->domains = [['domain' => 'example.com']];
    $organization->saveQuietly();

    $contact->organization()->associate($otherOrganization);
    $contact->save();

    app(MatchContactToOrganization::class)($staleContact);

    expect($contact->refresh()->organization_id)->toBe($otherOrganization->getKey());
});

it('matches the current email when a stale contact is passed', function (?string $currentEmail, bool $targeted, bool $matches) {
    $oldOrganization = Organization::factory()->create(['domains' => []]);
    $currentOrganization = Organization::factory()->create(['domains' => []]);
    $contact = Contact::factory()->create([
        'email' => 'person@old.example',
        'organization_id' => null,
    ]);
    $staleContact = Contact::query()->findOrFail($contact->getKey());

    $oldOrganization->domains = [['domain' => 'old.example']];
    $oldOrganization->saveQuietly();
    $currentOrganization->domains = [['domain' => 'current.example']];
    $currentOrganization->saveQuietly();
    $contact->email = $currentEmail;
    $contact->saveQuietly();

    expect($contact->refresh()->organization_id)->toBeNull();

    app(MatchContactToOrganization::class)($staleContact, $targeted ? $oldOrganization : null);

    expect($contact->refresh()->email)->toBe($currentEmail)
        ->and($contact->organization_id)->toBe($matches ? $currentOrganization->getKey() : null);
})->with([
    'changed to a matching domain' => ['person@current.example', false, true],
    'changed to an unmatched domain' => ['person@unmatched.example', false, false],
    'cleared' => [null, false, false],
    'changed away from the targeted organization' => ['person@current.example', true, false],
]);
