<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Actions\FindOrganizationByEmailDomain;

test('it returns organization for exact matching domain', function () {
    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('myemail@example.com');

    expect($result)->not->toBeNull()
        ->and($result?->is($organization))->toBeTrue();
});

test('it matches case-insensitive email domain', function () {
    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('myemail@EXAMPLE.COM');

    expect($result)->not->toBeNull()
        ->and($result?->is($organization))->toBeTrue();
});

test('it normalizes stored www domain for matching', function () {
    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'www.example.com'],
        ],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('myemail@example.com');

    expect($result)->not->toBeNull()
        ->and($result?->is($organization))->toBeTrue();
});

test('it does not match when only email domain includes www', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('myemail@www.example.com');

    expect($result)->toBeNull();
});

test('it ignores organizations where contact generation is disabled', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => false,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('myemail@example.com');

    expect($result)->toBeNull();
});

test('it returns null for invalid email format', function () {
    $action = app(FindOrganizationByEmailDomain::class);

    expect($action('invalid-email'))->toBeNull()
        ->and($action('myemail@'))->toBeNull();
});
