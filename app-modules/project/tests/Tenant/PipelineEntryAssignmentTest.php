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

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Notifications\PipelineEntryAssignedToUserNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

use function Tests\asSuperAdmin;

beforeEach(function () {
    Notification::fake();
});

it('sends notification to assigned user when pipeline entry is created with assigned_to', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $user->getMorphClass(),
        'assigned_to_id' => $user->id,
    ]);

    Notification::assertSentTo($user, PipelineEntryAssignedToUserNotification::class);
});

it('does not send notification when pipeline entry is created without assigned_to', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => null,
        'assigned_to_id' => null,
    ]);

    Notification::assertNothingSent();
});

it('sends notification to newly assigned user when assigned_to changes', function () {
    asSuperAdmin();

    $originalUser = User::factory()->create();
    $newUser = User::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $originalUser->getMorphClass(),
        'assigned_to_id' => $originalUser->id,
    ]);

    Notification::assertSentTo($originalUser, PipelineEntryAssignedToUserNotification::class);
    Notification::assertNotSentTo($newUser, PipelineEntryAssignedToUserNotification::class);

    Notification::fake();

    $entry->fresh()->update([
        'assigned_to_type' => $newUser->getMorphClass(),
        'assigned_to_id' => $newUser->id,
    ]);

    Notification::assertSentTo($newUser, PipelineEntryAssignedToUserNotification::class);
    Notification::assertNotSentTo($originalUser, PipelineEntryAssignedToUserNotification::class);
});

it('does not send notification when a non-assignment field changes', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => null,
        'assigned_to_id' => null,
    ]);

    $entry->fresh()->update(['description' => 'New description']);

    Notification::assertNothingSent();
});

it('does not send notification when assigned_to is unchanged on update', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $user->getMorphClass(),
        'assigned_to_id' => $user->id,
    ]);

    Notification::assertSentTo($user, PipelineEntryAssignedToUserNotification::class);

    Notification::fake();

    $entry->fresh()->update(['assigned_to_id' => $user->id, 'name' => 'New name']);

    Notification::assertNotSentTo($user, PipelineEntryAssignedToUserNotification::class);
});
