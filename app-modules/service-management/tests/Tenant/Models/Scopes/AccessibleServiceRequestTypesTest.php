<?php

use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Models\Scopes\AccessibleServiceRequestTypes;
use AidingApp\ServiceManagement\Models\Scopes\AuditedServiceRequestTypes;
use AidingApp\ServiceManagement\Models\Scopes\ManagedServiceRequestTypes;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;

it('resolves manager and auditor types through groups', function () {
    $user = User::factory()->create();

    $managerType = ServiceRequestType::factory()->create();
    $managerGroup = Group::factory()->create();
    $managerGroup->users()->attach($user);
    $managerType->managerGroups()->attach($managerGroup);

    $auditorType = ServiceRequestType::factory()->create();
    $auditorGroup = Group::factory()->create();
    $auditorGroup->users()->attach($user);
    $auditorType->auditorGroups()->attach($auditorGroup);

    $managedIds = ServiceRequestType::query()->tap(new ManagedServiceRequestTypes($user))->pluck('id');
    $auditedIds = ServiceRequestType::query()->tap(new AuditedServiceRequestTypes($user))->pluck('id');
    $accessibleIds = ServiceRequestType::query()->tap(new AccessibleServiceRequestTypes($user))->pluck('id');

    expect($managedIds->all())->toContain($managerType->getKey())
        ->and($managedIds->all())->not->toContain($auditorType->getKey())
        ->and($auditedIds->all())->toContain($auditorType->getKey())
        ->and($auditedIds->all())->not->toContain($managerType->getKey())
        ->and($accessibleIds->all())->toContain($managerType->getKey(), $auditorType->getKey())
        ->and($managerType->isManagedBy($user))->toBeTrue()
        ->and($managerType->isAuditedBy($user))->toBeFalse()
        ->and($auditorType->isAuditedBy($user))->toBeTrue();
});
