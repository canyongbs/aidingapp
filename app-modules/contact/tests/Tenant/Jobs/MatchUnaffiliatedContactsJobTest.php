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
use AidingApp\Contact\Jobs\MatchUnaffiliatedContactsJob;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\OrganizationEmailDomainLookup;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

it('matches unaffiliated contacts only to the targeted organization', function () {
    Queue::fake([MatchUnaffiliatedContactsJob::class]);
    $organization = Organization::factory()->create([
        'domains' => [],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [],
    ]);
    $matchingContact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $otherDomainContact = Contact::factory()->create([
        'email' => 'person@other.com',
        'organization_id' => null,
    ]);
    $assignedContact = Contact::factory()
        ->for($otherOrganization, 'organization')
        ->create(['email' => 'assigned@example.com']);

    $otherOrganization->domains = [['domain' => 'other.com']];
    $otherOrganization->save();
    $organization->domains = [['domain' => 'example.com']];
    $organization->save();

    (new MatchUnaffiliatedContactsJob((string) $organization->getKey()))
        ->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));

    expect($matchingContact->refresh()->organization_id)->toBe($organization->getKey())
        ->and($otherDomainContact->refresh()->organization_id)->toBeNull()
        ->and($assignedContact->refresh()->organization_id)->toBe($otherOrganization->getKey());
});

it('matches unaffiliated contacts to any matching organization during reconciliation', function () {
    Queue::fake([MatchUnaffiliatedContactsJob::class]);
    $organization = Organization::factory()->create([
        'domains' => [],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [],
    ]);
    $matchingContact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $unmatchedContact = Contact::factory()->create([
        'email' => 'person@unmatched.com',
        'organization_id' => null,
    ]);
    $otherMatchingContact = Contact::factory()->create([
        'email' => 'person@other.com',
        'organization_id' => null,
    ]);
    $assignedContact = Contact::factory()
        ->for($otherOrganization, 'organization')
        ->create(['email' => 'assigned@example.com']);

    $organization->domains = [['domain' => 'example.com']];
    $organization->save();
    $otherOrganization->domains = [['domain' => 'other.com']];
    $otherOrganization->save();

    (new MatchUnaffiliatedContactsJob())->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));

    expect($matchingContact->refresh()->organization_id)->toBe($organization->getKey())
        ->and($otherMatchingContact->refresh()->organization_id)->toBe($otherOrganization->getKey())
        ->and($unmatchedContact->refresh()->organization_id)->toBeNull()
        ->and($assignedContact->refresh()->organization_id)->toBe($otherOrganization->getKey());
});

it('skips unmatched domains without per-contact lookups or locks', function (bool $targeted) {
    $organization = Organization::factory()->create(['domains' => []]);
    $matchingContact = Contact::factory()->create([
        'email' => 'person@EXAMPLE.COM',
        'organization_id' => null,
    ]);
    $unmatchedContacts = Contact::factory()->count(101)
        ->sequence(fn (Sequence $sequence): array => [
            'email' => "person{$sequence->index}@unmatched.example",
        ])
        ->create(['organization_id' => null]);
    $organization->domains = [['domain' => 'https://www.example.com:8443/path']];
    $organization->saveQuietly();
    $job = new MatchUnaffiliatedContactsJob($targeted ? (string) $organization->getKey() : null);
    $connection = DB::connection('tenant');
    $connection->enableQueryLog();
    $connection->flushQueryLog();

    try {
        $job->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));
        $queries = collect($connection->getQueryLog())->pluck('query');
    } finally {
        $connection->disableQueryLog();
        $connection->flushQueryLog();
    }

    expect($matchingContact->refresh()->organization_id)->toBe($organization->getKey())
        ->and(Contact::query()->whereKey($unmatchedContacts->modelKeys())->whereNotNull('organization_id')->exists())->toBeFalse()
        ->and($queries->filter(fn (string $query): bool => str_contains($query, 'jsonb_array_elements')))->toHaveCount(2)
        ->and($queries->filter(fn (string $query): bool => str_contains($query, 'for update')))->toHaveCount(1);
})->with([
    'full sweep' => false,
    'targeted sweep' => true,
]);

it('verifies current organization domains after the candidate snapshot', function (bool $targeted) {
    $organization = Organization::factory()->create(['domains' => []]);
    $contact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $organization->domains = [['domain' => 'example.com']];
    $organization->saveQuietly();
    $changedAfterSnapshot = false;
    DB::listen(function (QueryExecuted $query) use ($organization, &$changedAfterSnapshot): void {
        if (! $changedAfterSnapshot && str_contains($query->sql, 'AS normalized_domain')) {
            $changedAfterSnapshot = true;
            $organization->domains = [['domain' => 'other.example']];
            $organization->saveQuietly();
        }
    });

    (new MatchUnaffiliatedContactsJob($targeted ? (string) $organization->getKey() : null))
        ->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));

    expect($changedAfterSnapshot)->toBeTrue()
        ->and($contact->refresh()->organization_id)->toBeNull();
})->with([
    'full sweep' => false,
    'targeted sweep' => true,
]);

it('does not scan contacts when there are no candidate domains', function () {
    Organization::factory()->create(['domains' => []]);
    $contact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $connection = DB::connection('tenant');
    $connection->enableQueryLog();
    $connection->flushQueryLog();

    try {
        (new MatchUnaffiliatedContactsJob())->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));
        $queries = $connection->getQueryLog();
    } finally {
        $connection->disableQueryLog();
        $connection->flushQueryLog();
    }

    expect($queries)->toHaveCount(1)
        ->and($contact->refresh()->organization_id)->toBeNull();
});

it('does not match contacts when the targeted organization has been deleted', function (bool $forceDelete) {
    $organization = Organization::factory()->create(['domains' => []]);
    $otherOrganization = Organization::factory()->create(['domains' => []]);
    $contact = Contact::factory()->create([
        'email' => 'person@example.com',
        'organization_id' => null,
    ]);
    $job = new MatchUnaffiliatedContactsJob((string) $organization->getKey());

    $otherOrganization->domains = [['domain' => 'example.com']];
    $otherOrganization->saveQuietly();

    if ($forceDelete) {
        $organization->forceDelete();
    } else {
        $organization->delete();
    }

    expect($contact->refresh()->organization_id)->toBeNull();

    $job->handle(app(MatchContactToOrganization::class), app(OrganizationEmailDomainLookup::class));

    expect($contact->refresh()->organization_id)->toBeNull();
})->with([
    'soft deleted' => false,
    'permanently deleted' => true,
]);

it('does not run reconciliation when another reconciliation for the tenant holds the lock', function () {
    $job = new MatchUnaffiliatedContactsJob();
    $middleware = $job->middleware()[0];

    assert($middleware instanceof WithoutOverlapping);

    $tenant = Tenant::current();
    assert($tenant instanceof Tenant);

    expect($middleware->getLockKey($job))->toContain((string) $tenant->getKey());
    expect($middleware->expiresAfter)->toBe(180);

    $job->withFakeQueueInteractions();
    $lock = Cache::lock($middleware->getLockKey($job), $middleware->expiresAfter);
    expect($lock->get())->toBeTrue();

    $handled = false;

    try {
        $middleware->handle($job, function () use (&$handled): void {
            $handled = true;
        });
    } finally {
        $lock->release();
    }

    expect($handled)->toBeFalse();

    $job->assertNotReleased();
});
