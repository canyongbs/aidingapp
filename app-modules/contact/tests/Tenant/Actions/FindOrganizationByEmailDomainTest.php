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

use AidingApp\Contact\Actions\FindOrganizationByEmailDomain;
use AidingApp\Contact\Models\Organization;

it('matches an exact email domain', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('person@example.com');

    expect($result?->is($organization))->toBeTrue();
});

it('matches email domains without regard to case', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('person@EXAMPLE.COM');

    expect($result?->is($organization))->toBeTrue();
});

it('normalizes stored www domains before matching', function () {
    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => false,
        'domains' => [['domain' => 'www.example.com']],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('person@example.com');

    expect($result?->is($organization))->toBeTrue();
});

it('normalizes stored URLs with a path and port before matching', function () {
    $organization = Organization::factory()->create([
        'domains' => [['domain' => 'https://www.example.com:8443/path']],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('person@example.com');

    expect($result?->is($organization))->toBeTrue();
});

it('returns no organization for an invalid email address', function () {
    $action = app(FindOrganizationByEmailDomain::class);

    expect($action('invalid-email'))->toBeNull()
        ->and($action('person@'))->toBeNull();
});

it('returns no organization when the email domain does not match', function () {
    Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);

    $result = app(FindOrganizationByEmailDomain::class)('person@other.com');

    expect($result)->toBeNull();
});

it('limits matching to the supplied organization', function () {
    $matchingOrganization = Organization::factory()->create([
        'domains' => [['domain' => 'example.com']],
    ]);
    $otherOrganization = Organization::factory()->create([
        'domains' => [['domain' => 'other.com']],
    ]);
    $action = app(FindOrganizationByEmailDomain::class);

    expect($action('person@example.com', $matchingOrganization)?->is($matchingOrganization))->toBeTrue()
        ->and($action('person@example.com', $otherOrganization))->toBeNull();
});
