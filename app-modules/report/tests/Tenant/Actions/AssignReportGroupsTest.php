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
use AidingApp\Report\Actions\AssignReportGroups;
use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Models\ReportGroupAccess;

it('assigns groups to a report without removing existing assignments', function (): void {
    $existingGroup = Group::factory()->create();
    $newGroup = Group::factory()->create();

    ReportGroupAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'group_id' => $existingGroup->getKey(),
    ]);

    app(AssignReportGroups::class)(
        ReportAccessKey::AiSupportAssistant->value,
        [$existingGroup->getKey(), $newGroup->getKey()],
        removeUnlisted: false,
    );

    expect(
        ReportGroupAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->pluck('group_id')
            ->all()
    )->toEqualCanonicalizing([$existingGroup->getKey(), $newGroup->getKey()]);
});

it('removes groups that are not listed when told to', function (): void {
    $removedGroup = Group::factory()->create();
    $keptGroup = Group::factory()->create();

    ReportGroupAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'group_id' => $removedGroup->getKey(),
    ]);

    app(AssignReportGroups::class)(
        ReportAccessKey::AiSupportAssistant->value,
        [$keptGroup->getKey()],
        removeUnlisted: true,
    );

    expect(
        ReportGroupAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->pluck('group_id')
            ->all()
    )->toBe([$keptGroup->getKey()]);
});

it('does not affect assignments for other reports', function (): void {
    $group = Group::factory()->create();

    ReportGroupAccess::factory()->create([
        'report_key' => ReportAccessKey::Sla->value,
        'group_id' => $group->getKey(),
    ]);

    app(AssignReportGroups::class)(ReportAccessKey::AiSupportAssistant->value, [], removeUnlisted: true);

    expect(
        ReportGroupAccess::query()
            ->where('report_key', ReportAccessKey::Sla->value)
            ->where('group_id', $group->getKey())
            ->exists()
    )->toBeTrue();
});
