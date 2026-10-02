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
      same in return. Canyon GBS® and Aiding App® are registered trademarks, and we are
      committed to enforcing and protecting our trademarks vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.

</COPYRIGHT>
*/

use AidingApp\Contact\Actions\MatchContactToOrganization;
use AidingApp\Contact\Jobs\MatchUnaffiliatedContactsJob;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use App\Models\Tenant;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
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
        ->handle(app(MatchContactToOrganization::class));

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
    $assignedContact = Contact::factory()
        ->for($otherOrganization, 'organization')
        ->create(['email' => 'assigned@example.com']);

    $organization->domains = [['domain' => 'example.com']];
    $organization->save();
    $otherOrganization->domains = [['domain' => 'other.com']];
    $otherOrganization->save();

    (new MatchUnaffiliatedContactsJob())->handle(app(MatchContactToOrganization::class));

    expect($matchingContact->refresh()->organization_id)->toBe($organization->getKey())
        ->and($unmatchedContact->refresh()->organization_id)->toBeNull()
        ->and($assignedContact->refresh()->organization_id)->toBe($otherOrganization->getKey());
});

it('does not run reconciliation when another reconciliation for the tenant holds the lock', function () {
    $job = new MatchUnaffiliatedContactsJob();
    $middleware = $job->middleware()[0];

    assert($middleware instanceof WithoutOverlapping);

    $tenant = Tenant::current();
    assert($tenant instanceof Tenant);

    expect($middleware->getLockKey($job))->toContain((string) $tenant->getKey());

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
});
