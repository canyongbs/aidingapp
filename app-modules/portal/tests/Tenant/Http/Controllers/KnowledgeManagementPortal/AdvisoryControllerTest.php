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
use AidingApp\Department\Models\Department;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Features\AdvisorySeveritySortFeature;
use App\Settings\LicenseSettings;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    actingAs(Contact::factory()->create());
});

it('lists the active advisories by default', function () {
    $activeAdvisories = Advisory::factory()->count(2)->for(AdvisoryStatus::factory()->open(), 'status')->create();
    $resolvedAdvisory = Advisory::factory()->for(AdvisoryStatus::factory()->resolved(), 'status')->create();

    $ids = collect(getJson(route('api.portal.advisories'))->assertOk()->json('data'))->pluck('id');

    expect($ids->sort()->values()->all())->toBe($activeAdvisories->pluck('id')->sort()->values()->all())
        ->and($ids)->not->toContain($resolvedAdvisory->getKey());
});

it('can list the resolved advisories', function () {
    Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create();
    $resolvedAdvisory = Advisory::factory()->for(AdvisoryStatus::factory()->resolved(), 'status')->create();

    getJson(route('api.portal.advisories', ['classification' => 'resolved']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $resolvedAdvisory->getKey());
});

it('displays the advisory data', function () {
    $severity = AdvisorySeverity::factory()->create(['name' => 'Major', 'color' => 'orange']);
    $status = AdvisoryStatus::factory()->open()->create(['name' => 'Mitigating']);

    $advisory = Advisory::factory()
        ->for($severity, 'severity')
        ->for($status, 'status')
        ->create([
            'title' => 'Science Hall flooding',
            'description' => 'A burst pipe has flooded the ground floor.',
        ]);

    getJson(route('api.portal.advisories'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $advisory->getKey())
        ->assertJsonPath('data.0.title', 'Science Hall flooding')
        ->assertJsonPath('data.0.description', 'A burst pipe has flooded the ground floor.')
        ->assertJsonPath('data.0.severity', ['id' => $severity->getKey(), 'name' => 'Major', 'color' => 'orange'])
        ->assertJsonPath('data.0.status', ['id' => $status->getKey(), 'name' => 'Mitigating', 'classification' => 'open']);
});

it('resolves the last updated date from the latest update', function () {
    $createdAt = Carbon::parse('2026-10-08 07:00:00', 'UTC');

    $advisoryWithUpdates = Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['created_at' => $createdAt]);
    AdvisoryUpdate::factory()->public()->for($advisoryWithUpdates, 'advisory')->create(['date' => $createdAt->copy()->addHour()]);
    AdvisoryUpdate::factory()->public()->for($advisoryWithUpdates, 'advisory')->create(['date' => $createdAt->copy()->addHours(2)]);

    $advisoryWithoutUpdates = Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['created_at' => $createdAt]);

    $lastUpdatedAt = collect(getJson(route('api.portal.advisories'))->assertOk()->json('data'))
        ->pluck('last_updated_at', 'id');

    expect($lastUpdatedAt[$advisoryWithUpdates->getKey()])->toBe('2026-10-08T09:00:00+00:00')
        ->and($lastUpdatedAt[$advisoryWithoutUpdates->getKey()])->toBe('2026-10-08T07:00:00+00:00');
});

it('returns the advisory counts for each classification regardless of the search and filters', function () {
    Advisory::factory()->count(3)->for(AdvisoryStatus::factory()->open(), 'status')->create();
    Advisory::factory()->count(2)->for(AdvisoryStatus::factory()->resolved(), 'status')->create();

    getJson(route('api.portal.advisories', ['search' => 'no advisory matches this']))
        ->assertOk()
        ->assertJsonCount(0, 'data')
        ->assertJsonPath('counts', ['open' => 3, 'resolved' => 2]);
});

it('returns the severity options ranked from highest to lowest', function () {
    $minor = AdvisorySeverity::factory()->create(['sort' => 2]);
    $critical = AdvisorySeverity::factory()->create(['sort' => 1]);

    $severityIds = collect(getJson(route('api.portal.advisories'))->assertOk()->json('severities'))->pluck('id');

    expect($severityIds->all())->toBe([$critical->getKey(), $minor->getKey()]);
});

it('returns the status options for the requested classification', function () {
    AdvisoryStatus::factory()->open()->create();
    $resolvedStatus = AdvisoryStatus::factory()->resolved()->create();

    getJson(route('api.portal.advisories', ['classification' => 'resolved']))
        ->assertOk()
        ->assertJsonCount(1, 'statuses')
        ->assertJsonPath('statuses.0.id', $resolvedStatus->getKey());
});

it('can search advisories', function (Advisory $matchingAdvisory) {
    Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create();

    getJson(route('api.portal.advisories', ['search' => 'FLOODED']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingAdvisory->getKey());
})->with([
    'title' => fn () => Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['title' => 'Basement flooded']),
    'description' => fn () => Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['description' => 'The basement has flooded.']),
    'department' => fn () => Advisory::factory()
        ->for(AdvisoryStatus::factory()->open(), 'status')
        ->for(Department::factory()->state(['name' => 'Flooded Basement Team']), 'assignedDepartment')
        ->create(),
    'update title' => fn () => Advisory::factory()
        ->for(AdvisoryStatus::factory()->open(), 'status')
        ->has(AdvisoryUpdate::factory()->public()->state(['title' => 'Basement flooded']), 'advisoryUpdates')
        ->create(),
    'update description' => fn () => Advisory::factory()
        ->for(AdvisoryStatus::factory()->open(), 'status')
        ->has(AdvisoryUpdate::factory()->public()->state(['update' => 'The basement has flooded.']), 'advisoryUpdates')
        ->create(),
]);

it('ignores internal updates when resolving the last updated date', function () {
    $createdAt = Carbon::parse('2026-10-08 07:00:00', 'UTC');

    $advisory = Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['created_at' => $createdAt]);
    AdvisoryUpdate::factory()->internal()->for($advisory, 'advisory')->create(['date' => $createdAt->copy()->addHour()]);

    getJson(route('api.portal.advisories'))
        ->assertOk()
        ->assertJsonPath('data.0.last_updated_at', '2026-10-08T07:00:00+00:00');
});

it('does not resolve the last updated date to before the advisory was created', function () {
    $createdAt = Carbon::parse('2026-10-08 07:00:00', 'UTC');

    $advisory = Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create(['created_at' => $createdAt]);
    AdvisoryUpdate::factory()->public()->for($advisory, 'advisory')->create(['date' => $createdAt->copy()->subDay()]);

    getJson(route('api.portal.advisories'))
        ->assertOk()
        ->assertJsonPath('data.0.last_updated_at', '2026-10-08T07:00:00+00:00');
});

it('does not search internal updates', function (string $field) {
    Advisory::factory()
        ->for(AdvisoryStatus::factory()->open(), 'status')
        ->has(AdvisoryUpdate::factory()->internal()->state([$field => 'Basement flooded']), 'advisoryUpdates')
        ->create();

    getJson(route('api.portal.advisories', ['search' => 'flooded']))
        ->assertOk()
        ->assertJsonCount(0, 'data');
})->with(['title', 'update']);

it('treats search wildcards literally', function () {
    $status = AdvisoryStatus::factory()->open()->create();

    $matchingAdvisory = Advisory::factory()->for($status, 'status')->create(['title' => '100% outage']);
    Advisory::factory()->for($status, 'status')->create(['title' => '100 outage']);

    getJson(route('api.portal.advisories', ['search' => '100%']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingAdvisory->getKey());
});

it('can filter advisories by severity', function () {
    $status = AdvisoryStatus::factory()->open()->create();
    $severity = AdvisorySeverity::factory()->create();

    $matchingAdvisory = Advisory::factory()->for($status, 'status')->for($severity, 'severity')->create();
    Advisory::factory()->for($status, 'status')->create();

    getJson(route('api.portal.advisories', ['severity' => $severity->getKey()]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingAdvisory->getKey());
});

it('can filter advisories by status', function () {
    $status = AdvisoryStatus::factory()->open()->create();

    $matchingAdvisory = Advisory::factory()->for($status, 'status')->create();
    Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create();

    getJson(route('api.portal.advisories', ['status' => $status->getKey()]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingAdvisory->getKey());
});

it('paginates the advisories', function () {
    Advisory::factory()->count(12)->for(AdvisoryStatus::factory()->open(), 'status')->create();

    getJson(route('api.portal.advisories', ['page' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta', [
            'current_page' => 2,
            'last_page' => 2,
            'from' => 11,
            'to' => 12,
            'total' => 12,
            'per_page' => 10,
        ]);
});

it('validates the inputs', function (array $parameters, string $error) {
    getJson(route('api.portal.advisories', $parameters))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$error]);
})->with([
    'classification enum' => [['classification' => 'archived'], 'classification'],
    'search max' => [['search' => str_repeat('a', 256)], 'search'],
    'severity uuid' => [['severity' => 'not-a-uuid'], 'severity'],
    'status uuid' => [['status' => 'not-a-uuid'], 'status'],
    'sort enum' => [['sort' => 'name'], 'sort'],
]);

describe('sorting', function () {
    beforeEach(function () {
        $status = AdvisoryStatus::factory()->open()->create();

        $critical = AdvisorySeverity::factory()->create(['sort' => 1]);
        $minor = AdvisorySeverity::factory()->create(['sort' => 2]);

        $this->criticalAdvisory = Advisory::factory()->for($status, 'status')->for($critical, 'severity')->create(['created_at' => now()->subDays(3)]);
        $this->recentMinorAdvisory = Advisory::factory()->for($status, 'status')->for($minor, 'severity')->create(['created_at' => now()->subDay()]);
        $this->olderMinorAdvisory = Advisory::factory()->for($status, 'status')->for($minor, 'severity')->create(['created_at' => now()->subDays(2)]);
    });

    it('sorts advisories by the highest severity first by default', function () {
        $ids = collect(getJson(route('api.portal.advisories'))->assertOk()->json('data'))->pluck('id');

        expect($ids->all())->toBe([
            $this->criticalAdvisory->getKey(),
            $this->recentMinorAdvisory->getKey(),
            $this->olderMinorAdvisory->getKey(),
        ]);
    });

    it('can sort advisories by the lowest severity first', function () {
        $ids = collect(getJson(route('api.portal.advisories', ['sort' => 'lowest_severity']))->assertOk()->json('data'))->pluck('id');

        expect($ids->all())->toBe([
            $this->recentMinorAdvisory->getKey(),
            $this->olderMinorAdvisory->getKey(),
            $this->criticalAdvisory->getKey(),
        ]);
    });

    it('sorts advisories by the most recently updated when `AdvisorySeveritySortFeature` is inactive', function () {
        AdvisorySeveritySortFeature::deactivate();

        $ids = collect(getJson(route('api.portal.advisories'))->assertOk()->json('data'))->pluck('id');

        expect($ids->all())->toBe([
            $this->recentMinorAdvisory->getKey(),
            $this->olderMinorAdvisory->getKey(),
            $this->criticalAdvisory->getKey(),
        ]);
    });
});

describe('authorization', function () {
    it('denies access to guests', function () {
        auth()->logout();

        getJson(route('api.portal.advisories'))->assertUnauthorized();
    });

    it('denies access without the advisory management add-on', function () {
        $licenseSettings = app(LicenseSettings::class);
        $licenseSettings->data->addons->advisoryManagement = false;
        $licenseSettings->save();

        getJson(route('api.portal.advisories'))->assertForbidden();
    });
});
