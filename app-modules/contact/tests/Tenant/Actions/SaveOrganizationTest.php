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

use AidingApp\Contact\Actions\SaveOrganization;
use AidingApp\Contact\DataTransferObjects\OrganizationDataObject;
use AidingApp\Contact\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

it('takes the shared domains lock while saving organization domains', function (): void {
    $heldLocks = fn (): int => (int) DB::selectOne(
        "SELECT count(*) AS total FROM pg_locks WHERE locktype = 'advisory' AND pid = pg_backend_pid() AND objid::bigint = (hashtext('organization-domains')::bigint & 4294967295)",
    )->total;

    expect($heldLocks())->toBe(0);

    app(SaveOrganization::class)(OrganizationDataObject::fromData(['name' => 'Acme', 'domains' => ['acme.test']]));

    // The lock is transaction scoped, and the test itself runs inside a transaction that has not yet ended.
    expect($heldLocks())->toBe(1);
});

it('revalidates the domains under the lock and does not save a domain claimed by another organization', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'Shared.edu']]]);

    expect(fn () => app(SaveOrganization::class)(OrganizationDataObject::fromData(['name' => 'Acme', 'domains' => ['shared.edu']])))
        ->toThrow(ValidationException::class);

    expect(Organization::query()->where('name', 'Acme')->exists())->toBeFalse();
});

it('revalidates the domains of an updated organization but allows its own', function (): void {
    Organization::factory()->create(['domains' => [['domain' => 'taken.edu']]]);
    $organization = Organization::factory()->create(['domains' => [['domain' => 'Mine.edu']]]);
    $action = app(SaveOrganization::class);

    $action(OrganizationDataObject::fromData(['domains' => ['mine.edu', 'extra.edu']]), $organization);

    expect($organization->refresh()->domains)->toBe([['domain' => 'mine.edu'], ['domain' => 'extra.edu']]);

    expect(fn () => $action(OrganizationDataObject::fromData(['domains' => ['TAKEN.edu']]), $organization))
        ->toThrow(ValidationException::class);

    expect($organization->refresh()->domains)->toBe([['domain' => 'mine.edu'], ['domain' => 'extra.edu']]);
});
