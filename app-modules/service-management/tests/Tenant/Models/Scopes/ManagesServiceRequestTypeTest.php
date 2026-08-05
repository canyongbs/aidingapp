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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Models\Scopes\ManagesServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;

it('includes direct manager users of the type and excludes non-managers', function () {
    $type = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $type->managerUsers()->attach($manager);

    $nonManager = User::factory()->create();

    $ids = User::query()->tap(new ManagesServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->toContain($manager->getKey())
        ->and($ids->all())->not->toContain($nonManager->getKey());
});

it('includes users that belong to a manager department of the type', function () {
    $type = ServiceRequestType::factory()->create();

    $department = Department::factory()->create();
    $type->managerDepartments()->attach($department);

    $manager = User::factory()->create();
    $manager->department()->associate($department)->save();

    $nonManager = User::factory()->create();

    $ids = User::query()->tap(new ManagesServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->toContain($manager->getKey())
        ->and($ids->all())->not->toContain($nonManager->getKey());
});

it('does not include managers of a different type', function () {
    $type = ServiceRequestType::factory()->create();
    $otherType = ServiceRequestType::factory()->create();

    $otherManager = User::factory()->create();
    $otherType->managerUsers()->attach($otherManager);

    $ids = User::query()->tap(new ManagesServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->not->toContain($otherManager->getKey());
});
