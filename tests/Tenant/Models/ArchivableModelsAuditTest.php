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

use AidingApp\Group\Models\Group;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Database\Eloquent\Model;

dataset('archivable auditable models', [
    'Group' => fn (): Model => Group::factory()->create(),
    'Pipeline' => fn (): Model => Pipeline::factory()->create(),
    'PipelineStage' => fn (): Model => PipelineStage::factory()->for(Pipeline::factory())->create(),
    'PipelineEntry' => fn (): Model => PipelineEntry::factory()->create(['assigned_to_id' => null, 'assigned_to_type' => null]),
    'Project' => fn (): Model => Project::factory()->create(),
    'ProjectMilestone' => fn (): Model => ProjectMilestone::factory()->create(),
    'ServiceRequestStatus' => fn (): Model => ServiceRequestStatus::factory()->create(),
    'ServiceRequestType' => fn (): Model => ServiceRequestType::factory()->create(),
]);

it('records one `archived` audit and no `updated` audit when archived', function (Group|Pipeline|PipelineEntry|PipelineStage|Project|ProjectMilestone|ServiceRequestStatus|ServiceRequestType $model): void {
    expect($model->audits()->where('event', 'archived')->exists())->toBeFalse();

    $model->archive();

    $archivedAudits = $model->audits()->where('event', 'archived')->get();

    expect($archivedAudits)->toHaveCount(1)
        ->and($archivedAudits->first()->old_values['archived_at'])->toBeNull()
        ->and($archivedAudits->first()->new_values['archived_at'])->not->toBeNull()
        ->and($model->audits()->where('event', 'updated')->exists())->toBeFalse();
})->with('archivable auditable models');

it('records one `unarchived` audit and no `updated` audit when unarchived', function (Group|Pipeline|PipelineEntry|PipelineStage|Project|ProjectMilestone|ServiceRequestStatus|ServiceRequestType $model): void {
    $model->archive();

    expect($model->audits()->where('event', 'unarchived')->exists())->toBeFalse();

    $model->unarchive();

    $unarchivedAudits = $model->audits()->where('event', 'unarchived')->get();

    expect($unarchivedAudits)->toHaveCount(1)
        ->and($unarchivedAudits->first()->old_values['archived_at'])->not->toBeNull()
        ->and($unarchivedAudits->first()->new_values['archived_at'])->toBeNull()
        ->and($model->audits()->where('event', 'updated')->exists())->toBeFalse();
})->with('archivable auditable models');
