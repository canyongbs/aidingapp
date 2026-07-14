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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Filament\Pages\AiClarification;
use AidingApp\Report\Models\ReportDepartmentAccess;
use AidingApp\Report\Models\ReportUserAccess;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(AiClarification::class)->assertForbidden();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::AiClarification->value,
        'user_id' => $user->getKey(),
    ]);

    get(AiClarification::getUrl())->assertSuccessful();
});

it('grants access to a user belonging to a department that has been granted access', function () {
    $department = Department::factory()->create();

    $user = User::factory()->create(['department_id' => $department->getKey()]);

    actingAs($user);

    livewire(AiClarification::class)->assertForbidden();

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::AiClarification->value,
        'department_id' => $department->getKey(),
    ]);

    get(AiClarification::getUrl())->assertSuccessful();
});
