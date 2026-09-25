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

use AidingApp\ServiceManagement\Actions\DescribeAutomatedStatusUsage;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

function describeUsageOf(ServiceRequestStatus ...$statuses): ?string
{
    return app(DescribeAutomatedStatusUsage::class)(
        ServiceRequestStatus::query()->whereKey(array_map(fn (ServiceRequestStatus $status): string => $status->getKey(), $statuses)),
    );
}

it('does not warn when no service request type automates the status', function () {
    $status = ServiceRequestStatus::factory()->create();

    expect(describeUsageOf($status))->toBeNull();
});

it('names the single type automating one status', function () {
    $status = ServiceRequestStatus::factory()->create();

    ServiceRequestType::factory()->for($status, 'automatedStatus')->create(['name' => 'VPN Access Request']);

    expect(describeUsageOf($status))->toBe(
        'This status is used for automatic status changes by 1 service request type: VPN Access Request. Archiving will not stop that automation.'
    );
});

it('names every type automating one status in alphabetical order', function () {
    $status = ServiceRequestStatus::factory()->create();

    ServiceRequestType::factory()->for($status, 'automatedStatus')->create(['name' => 'Password Reset']);
    ServiceRequestType::factory()->for($status, 'automatedStatus')->create(['name' => 'Hardware Repair']);

    expect(describeUsageOf($status))->toBe(
        'This status is used for automatic status changes by 2 service request types: Hardware Repair, Password Reset. Archiving will not stop that automation.'
    );
});

it('caps the named types and counts the remainder', function () {
    $status = ServiceRequestStatus::factory()->create();

    foreach (['A Type', 'B Type', 'C Type', 'D Type', 'E Type'] as $name) {
        ServiceRequestType::factory()->for($status, 'automatedStatus')->create(['name' => $name]);
    }

    expect(describeUsageOf($status))->toBe(
        'This status is used for automatic status changes by 5 service request types: A Type, B Type, C Type and 2 others. Archiving will not stop that automation.'
    );
});

it('uses the singular form when exactly one type is unnamed', function () {
    $status = ServiceRequestStatus::factory()->create();

    foreach (['A Type', 'B Type', 'C Type', 'D Type'] as $name) {
        ServiceRequestType::factory()->for($status, 'automatedStatus')->create(['name' => $name]);
    }

    expect(describeUsageOf($status))->toBe(
        'This status is used for automatic status changes by 4 service request types: A Type, B Type, C Type and 1 other. Archiving will not stop that automation.'
    );
});

it('counts how many of several statuses are automated', function () {
    $automated = ServiceRequestStatus::factory()->create();
    $alsoAutomated = ServiceRequestStatus::factory()->create();
    $notAutomated = ServiceRequestStatus::factory()->create();

    ServiceRequestType::factory()->for($automated, 'automatedStatus')->create(['name' => 'Password Reset']);
    ServiceRequestType::factory()->for($alsoAutomated, 'automatedStatus')->create(['name' => 'VPN Access Request']);

    expect(describeUsageOf($automated, $alsoAutomated, $notAutomated))->toBe(
        '2 of the selected statuses are used for automatic status changes by 2 service request types: Password Reset, VPN Access Request. Archiving will not stop that automation.'
    );
});

it('uses the singular verb when only one of several statuses is automated', function () {
    $automated = ServiceRequestStatus::factory()->create();
    $notAutomated = ServiceRequestStatus::factory()->create();

    ServiceRequestType::factory()->for($automated, 'automatedStatus')->create(['name' => 'Password Reset']);

    expect(describeUsageOf($automated, $notAutomated))->toBe(
        '1 of the selected statuses is used for automatic status changes by 1 service request type: Password Reset. Archiving will not stop that automation.'
    );
});

it('does not warn when none of several statuses are automated', function () {
    $statuses = ServiceRequestStatus::factory()->count(3)->create();

    expect(describeUsageOf(...$statuses->all()))->toBeNull();
});
