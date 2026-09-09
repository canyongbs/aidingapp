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
use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Models\Scopes\AuditsServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ServiceRequestTypeGroupAssignmentsFeature;
use App\Models\User;

it('includes direct and department auditors of the type', function () {
    $type = ServiceRequestType::factory()->create();
    $directAuditor = User::factory()->create();
    $type->auditorUsers()->attach($directAuditor);

    $department = Department::factory()->create();
    $type->auditorDepartments()->attach($department);
    $departmentAuditor = User::factory()->create();
    $departmentAuditor->department()->associate($department)->save();

    $ids = User::query()->tap(new AuditsServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->toContain($directAuditor->getKey(), $departmentAuditor->getKey());
});

it('includes users that belong to an auditor group of the type', function () {
    $type = ServiceRequestType::factory()->create();
    $group = Group::factory()->create();
    $type->auditorGroups()->attach($group);

    $auditor = User::factory()->create();
    $group->users()->attach($auditor);

    $ids = User::query()->tap(new AuditsServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->toContain($auditor->getKey());
});

it('does not query auditor groups while the feature is inactive', function () {
    $type = ServiceRequestType::factory()->create();
    $group = Group::factory()->create();
    $type->auditorGroups()->attach($group);

    $auditor = User::factory()->create();
    $group->users()->attach($auditor);

    ServiceRequestTypeGroupAssignmentsFeature::deactivate();

    $ids = User::query()->tap(new AuditsServiceRequestType($type->getKey()))->pluck('id');

    expect($ids->all())->not->toContain($auditor->getKey());
});
