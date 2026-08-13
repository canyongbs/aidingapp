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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Models\Sla;
use App\Features\SlaWaitingExclusionFeature;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

if (! function_exists('recentUpdateCreatorInfo')) {
    function recentUpdateCreatorInfo(ServiceRequest $serviceRequest): string
    {
        return explode('<br><br>', (string) $serviceRequest->getRecentUpdateFormatted())[0];
    }
}

it('renders a contact update as Customer with the organization on its own line', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $contact = Contact::factory()
        ->for(Organization::factory()->state(['name' => 'Acme Corp']))
        ->create(['full_name' => 'Jane Doe']);

    ServiceRequestUpdate::factory()
        ->for($serviceRequest)
        ->for($contact, 'createdBy')
        ->create();

    expect(recentUpdateCreatorInfo($serviceRequest))->toBe('Jane Doe - Customer<br>Acme Corp');
});

it('renders a contact update with no organization as Customer only, without an N/A line', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $contact = Contact::factory()
        ->state(['full_name' => 'Jane Doe', 'organization_id' => null])
        ->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest)
        ->for($contact, 'createdBy')
        ->create();

    expect(recentUpdateCreatorInfo($serviceRequest))->toBe('Jane Doe - Customer')
        ->and((string) $serviceRequest->getRecentUpdateFormatted())->not->toContain('N/A');
});

it('renders a user update as Service Provider, unchanged', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = User::factory()->state(['name' => 'Sam Staff'])->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest)
        ->for($user, 'createdBy')
        ->create();

    expect(recentUpdateCreatorInfo($serviceRequest))->toBe('Sam Staff - Service Provider');
});

it('appends the file-attachment note when the update has uploads', function () {
    Storage::fake('s3');

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest)
        ->for(Contact::factory()->state(['full_name' => 'Jane Doe']), 'createdBy')
        ->create();

    $update->addMedia(UploadedFile::fake()->image('report.png'))->toMediaCollection('uploads');

    expect((string) $serviceRequest->getRecentUpdateFormatted())
        ->toContain('<br><br>Note: Files were attached with this update. Please login the service portal to see the files.');
});

describe('SLA waiting exclusion', function () {
    it('excludes waiting and closed time from the resolution seconds when the feature is active', function () {
        Notification::fake();

        $open = ServiceRequestStatus::factory()->open()->create();
        $waiting = ServiceRequestStatus::factory()->waiting()->create();
        $inProgress = ServiceRequestStatus::factory()->inProgress()->create();
        $closed = ServiceRequestStatus::factory()->closed()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create(['status_id' => $open->getKey()]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $waiting->getKey()]);

        $this->travelTo($start->addSeconds(150));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $inProgress->getKey()]);

        $this->travelTo($start->addSeconds(180));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        // Total window is 180s; the 50s waiting span is excluded, the closed span is zero-length.
        expect($serviceRequest->fresh()->getResolutionSeconds())->toBe(130);
    });

    it('includes waiting and closed time in the resolution seconds when the feature is inactive', function () {
        Notification::fake();
        SlaWaitingExclusionFeature::deactivate();

        $open = ServiceRequestStatus::factory()->open()->create();
        $waiting = ServiceRequestStatus::factory()->waiting()->create();
        $inProgress = ServiceRequestStatus::factory()->inProgress()->create();
        $closed = ServiceRequestStatus::factory()->closed()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create(['status_id' => $open->getKey()]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $waiting->getKey()]);

        $this->travelTo($start->addSeconds(150));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $inProgress->getKey()]);

        $this->travelTo($start->addSeconds(180));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        expect($serviceRequest->fresh()->getResolutionSeconds())->toBe(180);
    });

    it('excludes waiting time from the latest response seconds when the feature is active', function () {
        Notification::fake();

        $open = ServiceRequestStatus::factory()->open()->create();
        $waiting = ServiceRequestStatus::factory()->waiting()->create();
        $inProgress = ServiceRequestStatus::factory()->inProgress()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create(['status_id' => $open->getKey()]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $waiting->getKey()]);

        $this->travelTo($start->addSeconds(150));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $inProgress->getKey()]);

        // Window is 150s with no inbound update; the 50s waiting span is excluded.
        expect($serviceRequest->fresh()->getLatestResponseSeconds())->toBe(100);
    });

    it('recomputes the stored time to resolution from the ledger on close when the feature is active', function () {
        Notification::fake();

        $open = ServiceRequestStatus::factory()->open()->create();
        $waiting = ServiceRequestStatus::factory()->waiting()->create();
        $closed = ServiceRequestStatus::factory()->closed()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create(['status_id' => $open->getKey()]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $waiting->getKey()]);

        $this->travelTo($start->addSeconds(150));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        // 150s window minus the 50s waiting span.
        expect($serviceRequest->fresh()->time_to_resolution)->toBe(100);
    });

    it('reports the resolution SLA as compliant once waiting time is excluded', function () {
        Notification::fake();

        $sla = Sla::create([
            'name' => 'Test SLA',
            'response_seconds' => 60,
            'resolution_seconds' => 150,
        ]);

        $priority = ServiceRequestPriority::factory()->create(['sla_id' => $sla->getKey()]);

        $open = ServiceRequestStatus::factory()->open()->create();
        $waiting = ServiceRequestStatus::factory()->waiting()->create();
        $inProgress = ServiceRequestStatus::factory()->inProgress()->create();
        $closed = ServiceRequestStatus::factory()->closed()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create([
            'status_id' => $open->getKey(),
            'priority_id' => $priority->getKey(),
        ]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $waiting->getKey()]);

        $this->travelTo($start->addSeconds(150));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $inProgress->getKey()]);

        $this->travelTo($start->addSeconds(180));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        // The raw 180s exceeds the 150s SLA, but excluding the 50s waiting span brings it to 130s.
        expect($serviceRequest->fresh()->getResolutionSlaComplianceStatus())->toBe(SlaComplianceStatus::Compliant);
    });

    it('excludes time spent closed before a reopen from the resolution seconds when the feature is active', function () {
        Notification::fake();

        $open = ServiceRequestStatus::factory()->open()->create();
        $inProgress = ServiceRequestStatus::factory()->inProgress()->create();
        $closed = ServiceRequestStatus::factory()->closed()->create();

        $start = CarbonImmutable::parse('2026-01-01 00:00:00');

        $this->travelTo($start);
        $serviceRequest = ServiceRequest::factory()->create(['status_id' => $open->getKey()]);

        $this->travelTo($start->addSeconds(100));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        $this->travelTo($start->addSeconds(200));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $inProgress->getKey()]);

        $this->travelTo($start->addSeconds(300));
        ServiceRequest::query()->findOrFail($serviceRequest->getKey())->update(['status_id' => $closed->getKey()]);

        // The 300s window excludes the 100s the request spent closed before it was reopened.
        expect($serviceRequest->fresh()->getResolutionSeconds())->toBe(200);
    });
});
