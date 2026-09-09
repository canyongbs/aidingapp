<?php

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
