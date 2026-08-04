<?php

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Filament\Tables\ManagersTable;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;

it('includes direct manager users of the type and excludes non-managers', function () {
    $type = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $type->managerUsers()->attach($manager);

    $nonManager = User::factory()->create();

    $ids = ManagersTable::query($type->getKey())->pluck('id');

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

    $ids = ManagersTable::query($type->getKey())->pluck('id');

    expect($ids->all())->toContain($manager->getKey())
        ->and($ids->all())->not->toContain($nonManager->getKey());
});

it('excludes the given excludeUserId', function () {
    $type = ServiceRequestType::factory()->create();

    $current = User::factory()->create();
    $other = User::factory()->create();
    $type->managerUsers()->attach([$current->getKey(), $other->getKey()]);

    $ids = ManagersTable::query($type->getKey(), $current->getKey())->pluck('id');

    expect($ids->all())->not->toContain($current->getKey())
        ->and($ids->all())->toContain($other->getKey());
});
