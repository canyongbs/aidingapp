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

use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Rules\ResolvableContactTypeName;

$errors = fn (string $name): array => validator(
    ['type' => $name],
    ['type' => [new ResolvableContactTypeName()]],
)->errors()->get('type');

it('passes for a name that matches exactly one type, ignoring case', function () use ($errors): void {
    ContactType::factory()->create(['name' => 'Customer']);

    expect($errors('CUSTOMER'))->toBeEmpty();
});

it('fails for a name that matches no type', function () use ($errors): void {
    ContactType::factory()->create(['name' => 'Customer']);

    expect($errors('Prospect'))->toBe(['The specified contact type does not exist.']);
});

it('fails for a name that matches a deleted type', function () use ($errors): void {
    ContactType::factory()->create(['name' => 'Customer'])->delete();

    expect($errors('Customer'))->toBe(['The specified contact type does not exist.']);
});

it('fails for a name that matches more than one type', function () use ($errors): void {
    ContactType::factory()->create(['name' => 'Customer']);
    ContactType::factory()->create(['name' => 'CUSTOMER']);

    expect($errors('customer'))->toBe(['The specified contact type name is ambiguous.']);
});

it('does not treat wildcard characters as wildcards', function () use ($errors): void {
    ContactType::factory()->create(['name' => 'VIPXContact']);

    expect($errors('VIP_Contact'))->toBe(['The specified contact type does not exist.']);
});
