<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
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

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Carbon;

it('can archive a model', function () {
    $type = ServiceRequestType::factory()->create();

    expect($type->isArchived())->toBeFalse();
    expect($type->archived_at)->toBeNull();

    $result = $type->archive();

    expect($result)->toBeTrue();
    expect($type->isArchived())->toBeTrue();
    expect($type->archived_at)->toBeInstanceOf(Carbon::class);
});

it('excludes archived models from default query', function () {
    $activeType = ServiceRequestType::factory()->create();
    $archivedType = ServiceRequestType::factory()->create();
    $archivedType->archive();

    $types = ServiceRequestType::all();

    expect($types)->toHaveCount(1);
    expect($types->first()->id)->toBe($activeType->id);
});

it('can include archived models with `withArchived` scope', function () {
    $activeType = ServiceRequestType::factory()->create();
    $archivedType = ServiceRequestType::factory()->create();
    $archivedType->archive();

    $types = ServiceRequestType::withArchived()->get();

    expect($types)->toHaveCount(2);
    expect($types->pluck('id')->toArray())->toContain($activeType->id, $archivedType->id);
});

it('can get only archived models with `onlyArchived` scope', function () {
    $activeType = ServiceRequestType::factory()->create();
    $archivedType = ServiceRequestType::factory()->create();
    $archivedType->archive();

    $types = ServiceRequestType::onlyArchived()->get();

    expect($types)->toHaveCount(1);
    expect($types->first()->id)->toBe($archivedType->id);
});

it('can unarchive a model', function () {
    $type = ServiceRequestType::factory()->create();
    $type->archive();

    expect($type->isArchived())->toBeTrue();

    $result = $type->unarchive();

    expect($result)->toBeTrue();
    expect($type->isArchived())->toBeFalse();
    expect($type->archived_at)->toBeNull();
});

it('fires archiving and archived events when archiving', function () {
    $archivingFired = false;
    $archivedFired = false;

    ServiceRequestType::archiving(function () use (&$archivingFired) {
        $archivingFired = true;
    });

    ServiceRequestType::archived(function () use (&$archivedFired) {
        $archivedFired = true;
    });

    $type = ServiceRequestType::factory()->create();
    $type->archive();

    expect($archivingFired)->toBeTrue();
    expect($archivedFired)->toBeTrue();
});

it('fires unarchiving and unarchived events when unarchiving', function () {
    $unarchivingFired = false;
    $unarchivedFired = false;

    ServiceRequestType::unarchiving(function () use (&$unarchivingFired) {
        $unarchivingFired = true;
    });

    ServiceRequestType::unarchived(function () use (&$unarchivedFired) {
        $unarchivedFired = true;
    });

    $type = ServiceRequestType::factory()->create();
    $type->archive();
    $type->unarchive();

    expect($unarchivingFired)->toBeTrue();
    expect($unarchivedFired)->toBeTrue();
});

it('can archive quietly without firing events', function () {
    $archivingFired = false;
    $archivedFired = false;

    ServiceRequestType::archiving(function () use (&$archivingFired) {
        $archivingFired = true;
    });

    ServiceRequestType::archived(function () use (&$archivedFired) {
        $archivedFired = true;
    });

    $type = ServiceRequestType::factory()->create();
    $type->archiveQuietly();

    expect($type->isArchived())->toBeTrue();
    expect($archivingFired)->toBeFalse();
    expect($archivedFired)->toBeFalse();
});

it('can unarchive quietly without firing events', function () {
    $unarchivingFired = false;
    $unarchivedFired = false;

    ServiceRequestType::unarchiving(function () use (&$unarchivingFired) {
        $unarchivingFired = true;
    });

    ServiceRequestType::unarchived(function () use (&$unarchivedFired) {
        $unarchivedFired = true;
    });

    $type = ServiceRequestType::factory()->create();
    $type->archiveQuietly();
    $type->unarchiveQuietly();

    expect($type->isArchived())->toBeFalse();
    expect($unarchivingFired)->toBeFalse();
    expect($unarchivedFired)->toBeFalse();
});

it('can prevent archiving by returning false from archiving event', function () {
    ServiceRequestType::archiving(function () {
        return false;
    });

    $type = ServiceRequestType::factory()->create();
    $result = $type->archive();

    expect($result)->toBeFalse();
    expect($type->isArchived())->toBeFalse();
});

it('can prevent unarchiving by returning false from unarchiving event', function () {
    ServiceRequestType::unarchiving(function () {
        return false;
    });

    $type = ServiceRequestType::factory()->create();
    $type->archiveQuietly();
    $result = $type->unarchive();

    expect($result)->toBeFalse();
    expect($type->isArchived())->toBeTrue();
});

it('updates the `updated_at` timestamp when archiving', function () {
    $type = ServiceRequestType::factory()->create();
    $originalUpdatedAt = $type->updated_at;

    Carbon::setTestNow(now()->addMinute());

    $type->archive();

    expect($type->updated_at->gt($originalUpdatedAt))->toBeTrue();

    Carbon::setTestNow();
});

it('can bulk archive models using query builder', function () {
    ServiceRequestType::factory()->count(3)->create();

    ServiceRequestType::query()->archive();

    expect(ServiceRequestType::count())->toBe(0);
    expect(ServiceRequestType::withArchived()->count())->toBe(3);
});

it('can bulk unarchive models using query builder', function () {
    ServiceRequestType::factory()->count(3)->create();
    ServiceRequestType::query()->archive();

    expect(ServiceRequestType::count())->toBe(0);

    ServiceRequestType::onlyArchived()->unarchive();

    expect(ServiceRequestType::count())->toBe(3);
});
