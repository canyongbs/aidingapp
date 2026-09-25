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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignmentHistoryRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\CarbonImmutable;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the assignment date on one line and the time on a separate line without duplication', function () {
    asSuperAdmin();

    $timezone = app(DisplaySettings::class)->getTimezone();

    $serviceRequest = ServiceRequest::factory()->create();

    $assignee = User::factory()->create();
    $serviceRequest->priority->type->managerUsers()->attach($assignee);

    $assignedAt = CarbonImmutable::parse('2026-09-24 22:39:00', 'UTC');

    $assignment = ServiceRequestAssignment::factory()->create([
        'service_request_id' => $serviceRequest->getKey(),
        'user_id' => $assignee->getKey(),
        'assigned_at' => $assignedAt,
    ]);

    $localAssignedAt = $assignedAt->copy()->setTimezone($timezone);

    $expectedDate = $localAssignedAt->format('M j, Y');
    $expectedTime = $localAssignedAt->format('g:i a (T)');
    $combined = $localAssignedAt->format('M j, Y g:i a (T)');

    livewire(AssignmentHistoryRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnFormattedStateSet('assigned_at', $expectedDate, $assignment)
        ->assertTableColumnHasDescription('assigned_at', $expectedTime, $assignment)
        ->assertTableColumnDoesNotHaveDescription('assigned_at', $combined, $assignment);
});
