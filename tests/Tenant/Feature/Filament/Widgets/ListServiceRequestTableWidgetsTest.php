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

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Filament\Widgets\ListServiceRequestTableWidgets;
use App\Models\User;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

it('searches the assigned to column by full name, including a quoted phrase', function (string $search): void {
    asSuperAdmin();

    $openStatus = ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open])->create();

    $matching = ServiceRequest::factory()->state(['status_id' => $openStatus->getKey()])->create();
    $other = ServiceRequest::factory()->state(['status_id' => $openStatus->getKey()])->create();

    foreach ([[$matching, 'Avery', 'Smith'], [$other, 'Jordan', 'Lee']] as [$serviceRequest, $firstName, $lastName]) {
        $assignee = User::factory()->create(['first_name' => $firstName, 'last_name' => $lastName]);

        $serviceRequest->priority->type->managerUsers()->attach($assignee);

        ServiceRequestAssignment::factory()->active()->create([
            'service_request_id' => $serviceRequest->getKey(),
            'user_id' => $assignee->getKey(),
        ]);
    }

    Livewire::test(ListServiceRequestTableWidgets::class)
        ->searchTable($search)
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
})->with([
    'first name' => 'Avery',
    'last name' => 'Smith',
    'full name' => 'Avery Smith',
    'quoted full name' => '"Avery Smith"',
]);
