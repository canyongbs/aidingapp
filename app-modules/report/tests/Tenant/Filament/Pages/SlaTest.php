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

use AidingApp\Department\Models\Department;
use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Filament\Pages\Sla;
use AidingApp\Report\Filament\Widgets\ResolutionSlaByClassificationDonutChart;
use AidingApp\Report\Filament\Widgets\ResponseSlaByClassificationDonutChart;
use AidingApp\Report\Filament\Widgets\SlaBreachesByServiceRequestTypeTable;
use AidingApp\Report\Filament\Widgets\SlaComplianceOverTimeLineChart;
use AidingApp\Report\Filament\Widgets\SlaPerformanceByAgentTable;
use AidingApp\Report\Filament\Widgets\SlaStats;
use AidingApp\Report\Models\ReportDepartmentAccess;
use AidingApp\Report\Models\ReportUserAccess;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\Sla as SlaModel;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Enable the Service Management addon and grant the given user access to the
 * SLA report so the page can be mounted in tests.
 */
function grantSlaReportAccess(User $user): void
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::Sla->value,
        'user_id' => $user->getKey(),
    ]);
}

/**
 * Build a priority backed by an SLA with the given response/resolution thresholds.
 */
function slaReportPriority(?ServiceRequestType $type = null, int $responseSeconds = 3600, int $resolutionSeconds = 7200): ServiceRequestPriority
{
    $type ??= ServiceRequestType::factory()->create();

    $sla = SlaModel::create([
        'name' => 'SLA ' . Str::random(8),
        'response_seconds' => $responseSeconds,
        'resolution_seconds' => $resolutionSeconds,
    ]);

    return ServiceRequestPriority::factory()->create([
        'type_id' => $type->getKey(),
        'sla_id' => $sla->getKey(),
    ]);
}

/**
 * Create a service request that either breaches (created long ago) or complies (created just now)
 * with both the response and resolution SLA.
 */
function makeSlaServiceRequest(
    ServiceRequestPriority $priority,
    ServiceRequestStatus $status,
    ServiceRequestCategory $category,
    bool $breaching,
): ServiceRequest {
    return ServiceRequest::factory()->create([
        'priority_id' => $priority->getKey(),
        'status_id' => $status->getKey(),
        'category' => $category,
        'created_at' => $breaching ? now()->subDays(5) : now()->subMinute(),
    ]);
}

function openServiceRequestStatus(): ServiceRequestStatus
{
    return ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);
}

function streamedContent(StreamedResponse $response): string
{
    ob_start();

    $response->sendContent();

    return (string) ob_get_clean();
}

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create(['timezone' => 'UTC']);

    actingAs($user);

    livewire(Sla::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(Sla::class)->assertForbidden();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::Sla->value,
        'user_id' => $user->getKey(),
    ]);

    livewire(Sla::class)->assertOk();
});

it('grants access to a user belonging to a department that has been granted access', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $department = Department::factory()->create();

    $user = User::factory()->create(['timezone' => 'UTC', 'department_id' => $department->getKey()]);

    actingAs($user);

    livewire(Sla::class)->assertForbidden();

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::Sla->value,
        'department_id' => $department->getKey(),
    ]);

    livewire(Sla::class)->assertOk();
});

it('renders the report filters', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantSlaReportAccess($user);

    actingAs($user);

    livewire(Sla::class)
        ->assertOk()
        ->assertSee('Service Request Types')
        ->assertSee('Classification')
        ->assertSee('Assigned Agent');
});

it('renders every SLA widget on the page', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantSlaReportAccess($user);

    actingAs($user);

    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);

    livewire(Sla::class)->assertOk();

    livewire(SlaStats::class, ['cacheTag' => 'render-stats', 'pageFilters' => []])->assertOk();
    livewire(SlaComplianceOverTimeLineChart::class, ['cacheTag' => 'render-line', 'pageFilters' => []])->assertOk();
    livewire(ResponseSlaByClassificationDonutChart::class, ['cacheTag' => 'render-response', 'pageFilters' => []])->assertOk();
    livewire(ResolutionSlaByClassificationDonutChart::class, ['cacheTag' => 'render-resolution', 'pageFilters' => []])->assertOk();
    livewire(SlaPerformanceByAgentTable::class, ['cacheTag' => 'render-agent', 'pageFilters' => []])->assertOk();
    livewire(SlaBreachesByServiceRequestTypeTable::class, ['cacheTag' => 'render-type', 'pageFilters' => []])->assertOk();
});

it('computes the SLA stats for the filtered service requests', function () {
    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: false);

    $widget = new SlaStats();
    $widget->cacheTag = 'sla-stats-test';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual('3')
        ->and($stats[1]->getValue())->toEqual('2')
        ->and($stats[2]->getValue())->toEqual('66.7%')
        ->and($stats[3]->getValue())->toEqual('66.7%');
});

it('splits response SLA breaches by classification', function () {
    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: false);

    $widget = new ResponseSlaByClassificationDonutChart();
    $widget->cacheTag = 'response-sla-classification-test';
    $widget->pageFilters = [];

    $data = $widget->getData();

    expect($data['labels']->all())->toBe(['Incident', 'Request'])
        ->and($data['datasets'][0]['data']->all())->toBe([2, 1]);
});

it('splits resolution SLA breaches by classification', function () {
    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: false);

    $widget = new ResolutionSlaByClassificationDonutChart();
    $widget->cacheTag = 'resolution-sla-classification-test';
    $widget->pageFilters = [];

    $data = $widget->getData();

    expect($data['labels']->all())->toBe(['Incident', 'Request'])
        ->and($data['datasets'][0]['data']->all())->toBe([1, 1]);
});

it('plots response and resolution compliance over a rolling 12 months', function () {
    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: false);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: false);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);

    $widget = new SlaComplianceOverTimeLineChart();
    $widget->cacheTag = 'sla-over-time-test';
    $widget->pageFilters = [];

    $data = $widget->getData();

    $responseData = $data['datasets'][0]['data'];
    $resolutionData = $data['datasets'][1]['data'];

    expect($data['datasets'][0]['label'])->toBe('Response SLA')
        ->and($data['datasets'][0]['borderColor'])->toBe('#3b82f6')
        ->and($data['datasets'][1]['label'])->toBe('Resolution SLA')
        ->and($data['datasets'][1]['borderColor'])->toBe('#f97316')
        ->and($data['labels'])->toHaveCount(12)
        ->and(end($responseData))->toBe(50.0)
        ->and(end($resolutionData))->toBe(50.0);
});

it('groups SLA performance by assigned agent and exports it', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = slaReportPriority($type);
    $status = openServiceRequestStatus();

    $agentA = User::factory()->create(['name' => 'Agent A']);
    $agentB = User::factory()->create(['name' => 'Agent B']);

    $type->managerUsers()->attach([$agentA->getKey(), $agentB->getKey()]);

    $breachingOne = makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    $breachingTwo = makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    $compliant = makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: false);

    ServiceRequestAssignment::factory()->active()->create(['service_request_id' => $breachingOne->getKey(), 'user_id' => $agentA->getKey()]);
    ServiceRequestAssignment::factory()->active()->create(['service_request_id' => $breachingTwo->getKey(), 'user_id' => $agentA->getKey()]);
    ServiceRequestAssignment::factory()->active()->create(['service_request_id' => $compliant->getKey(), 'user_id' => $agentB->getKey()]);

    $widget = new SlaPerformanceByAgentTable();
    $widget->cacheTag = 'sla-agent-table-test';
    $widget->pageFilters = [];

    $csv = streamedContent($widget->exportCsv());

    expect($csv)
        ->toContain('Agent A')
        ->toContain('Agent B')
        ->toContain('0%')
        ->toContain('100%');
});

it('groups SLA breaches by service request type and exports it', function () {
    $status = openServiceRequestStatus();

    $typeX = ServiceRequestType::factory()->create(['name' => 'Type X']);
    $typeY = ServiceRequestType::factory()->create(['name' => 'Type Y']);

    $priorityX = slaReportPriority($typeX);
    $priorityY = slaReportPriority($typeY);

    makeSlaServiceRequest($priorityX, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priorityX, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priorityY, $status, ServiceRequestCategory::Request, breaching: false);

    $widget = new SlaBreachesByServiceRequestTypeTable();
    $widget->cacheTag = 'sla-type-table-test';
    $widget->pageFilters = [];

    $csv = streamedContent($widget->exportCsv());

    expect($csv)
        ->toContain('Type X')
        ->toContain('Type Y');
});

it('respects the classification filter in the SLA stats', function () {
    $priority = slaReportPriority();
    $status = openServiceRequestStatus();

    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Request, breaching: true);

    $widget = new SlaStats();
    $widget->cacheTag = 'sla-stats-classification-test';
    $widget->pageFilters = ['classification' => ServiceRequestCategory::Incident->value];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual('1');
});

it('respects the assigned agent filter in the SLA stats', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = slaReportPriority($type);
    $status = openServiceRequestStatus();

    $agentA = User::factory()->create(['name' => 'Agent A']);
    $agentB = User::factory()->create(['name' => 'Agent B']);

    $type->managerUsers()->attach([$agentA->getKey(), $agentB->getKey()]);

    $requestA = makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);
    $requestB = makeSlaServiceRequest($priority, $status, ServiceRequestCategory::Incident, breaching: true);

    ServiceRequestAssignment::factory()->active()->create(['service_request_id' => $requestA->getKey(), 'user_id' => $agentA->getKey()]);
    ServiceRequestAssignment::factory()->active()->create(['service_request_id' => $requestB->getKey(), 'user_id' => $agentB->getKey()]);

    $widget = new SlaStats();
    $widget->cacheTag = 'sla-stats-agent-test';
    $widget->pageFilters = ['assignedAgents' => [$agentA->getKey()]];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual('1');
});

it('respects the selected service request types in the SLA stats', function () {
    $status = openServiceRequestStatus();

    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = slaReportPriority($typeA);
    $priorityB = slaReportPriority($typeB);

    makeSlaServiceRequest($priorityA, $status, ServiceRequestCategory::Incident, breaching: true);
    makeSlaServiceRequest($priorityB, $status, ServiceRequestCategory::Incident, breaching: true);

    $widget = new SlaStats();
    $widget->cacheTag = 'sla-stats-type-test';
    $widget->pageFilters = ['serviceRequestTypes' => [$typeA->getKey()]];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual('1');
});
