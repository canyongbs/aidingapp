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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeTime;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

test('returns all service monitoring targets with latest history when portal is enabled', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $targets = ServiceMonitoringTarget::factory()
        ->count(3)
        ->sequence(
            ['name' => 'Google', 'domain' => 'https://google.com'],
            ['name' => 'Facebook', 'domain' => 'https://facebook.com'],
            ['name' => 'bing.com', 'domain' => 'https://bing.com'],
        )
        ->create();

    foreach ($targets as $target) {
        $target->histories()->create([
            'response_time' => 0.123,
            'succeeded' => true,
            'response' => 200,
        ]);
    }

    $url = URL::route(name: 'api.portal.status', absolute: false);
    $response = get($url);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(3);
});

test('excludes confidential service monitoring targets unless the contact is granted access', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    ServiceMonitoringTarget::factory()->create(['name' => 'Public Monitor']);

    $confidentialTarget = ServiceMonitoringTarget::factory()->confidential()->create([
        'name' => 'Confidential Monitor',
    ]);

    $url = URL::route(name: 'api.portal.status', absolute: false);

    $response = get($url);
    expect($response->json('data'))->toHaveCount(1);

    $confidentialTarget->confidentialContacts()->attach($contact->getKey());

    $response = get($url);
    expect($response->json('data'))->toHaveCount(2);
});

/**
 * @param array<string, mixed> $query
 */
function getServiceMonitorStatuses(array $query = []): TestResponse
{
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    actingAs(Contact::factory()->create());

    return getJson(URL::route('api.portal.status', $query, absolute: false));
}

it('lists service monitors with their status, uptime, and daily history', function () {
    freezeTime();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'name' => 'Customer Website',
        'description' => 'Monitors the public website via HTTPS',
        'frequency' => ServiceMonitoringFrequency::FiveMinutes,
    ]);

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()->subDays(40)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subDays(29)->subHours(12)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subMinutes(2)]);

    getServiceMonitorStatuses()
        ->assertOk()
        ->assertJsonPath('data.0.id', $serviceMonitoringTarget->getKey())
        ->assertJsonPath('data.0.name', 'Customer Website')
        ->assertJsonPath('data.0.description', 'Monitors the public website via HTTPS')
        ->assertJsonPath('data.0.monitor_type', 'availability')
        ->assertJsonPath('data.0.monitor_type_label', 'Availability')
        ->assertJsonPath('data.0.frequency_label', '5 minutes')
        ->assertJsonPath('data.0.status', 'operational')
        ->assertJsonPath('data.0.last_checked_at', now()->subMinutes(2)->toIso8601String())
        ->assertJsonPath('data.0.thirty_day_uptime_percentage', 100)
        ->assertJsonPath('data.0.twelve_month_uptime_percentage', null)
        ->assertJsonCount(30, 'data.0.history')
        ->assertJsonPath('data.0.history.29.status', 'operational')
        ->assertJsonPath('data.0.history.29.checks_count', 1)
        ->assertJsonPath('meta.total', 1);
});

it('summarizes every service monitor the contact can see, regardless of the search', function () {
    freezeTime();

    $operationalTarget = ServiceMonitoringTarget::factory()->create(['name' => 'Operational Monitor']);
    HistoricalServiceMonitoring::factory()->for($operationalTarget)->create(['created_at' => now()->subMinutes(10)]);

    $outageTarget = ServiceMonitoringTarget::factory()->create(['name' => 'Outage Monitor']);
    HistoricalServiceMonitoring::factory()->for($outageTarget)->failed()->create(['created_at' => now()->subMinutes(5)]);

    ServiceMonitoringTarget::factory()->create(['name' => 'Unchecked Monitor']);
    ServiceMonitoringTarget::factory()->confidential()->create(['name' => 'Confidential Monitor']);

    getServiceMonitorStatuses(['search' => 'Operational'])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('summary.status', 'degraded')
        ->assertJsonPath('summary.total_count', 3)
        ->assertJsonPath('summary.status_counts', [
            'operational' => 1,
            'degraded' => 0,
            'outage' => 1,
            'unknown' => 1,
        ])
        ->assertJsonPath('summary.last_checked_at', now()->subMinutes(5)->toIso8601String());
});

it('summarizes the service monitors as operational when none have issues', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create();

    ServiceMonitoringTarget::factory()->create();

    getServiceMonitorStatuses()
        ->assertOk()
        ->assertJsonPath('summary.status', 'operational');
});

it('summarizes the service monitors as unknown when none have been checked', function () {
    ServiceMonitoringTarget::factory()->count(2)->create();

    getServiceMonitorStatuses()
        ->assertOk()
        ->assertJsonPath('summary.status', 'unknown')
        ->assertJsonPath('summary.total_count', 2)
        ->assertJsonPath('summary.last_checked_at', null);
});

it('can search by name and description', function () {
    $matchingNameTarget = ServiceMonitoringTarget::factory()->create(['name' => 'Student Portal', 'description' => null]);
    $matchingDescriptionTarget = ServiceMonitoringTarget::factory()->create(['name' => 'Website', 'description' => 'The public portal']);
    $otherTarget = ServiceMonitoringTarget::factory()->create(['name' => 'Email', 'description' => 'Outbound mail']);

    getServiceMonitorStatuses(['search' => 'PORTAL'])
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $matchingNameTarget->getKey()])
        ->assertJsonFragment(['id' => $matchingDescriptionTarget->getKey()])
        ->assertJsonMissing(['id' => $otherTarget->getKey()]);
});

it('can sort by column', function (string $sort, string $direction, array $expectedOrder) {
    freezeTime();

    $targets = collect([
        'Alpha' => ['frequency' => ServiceMonitoringFrequency::OneHour, 'checks' => [8748 => false, 700 => true, 1 => true]],
        'Bravo' => ['frequency' => ServiceMonitoringFrequency::FiveMinutes, 'checks' => [8748 => true, 700 => true, 3 => false, 1 => true]],
        'Charlie' => ['frequency' => ServiceMonitoringFrequency::TwentyFourHours, 'checks' => [700 => true, 2 => false]],
        'Delta' => ['frequency' => ServiceMonitoringFrequency::FifteenMinutes, 'checks' => []],
    ])->map(function (array $attributes, string $name): ServiceMonitoringTarget {
        $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create(['name' => $name, 'frequency' => $attributes['frequency']]);

        foreach ($attributes['checks'] as $hoursAgo => $succeeded) {
            HistoricalServiceMonitoring::factory()
                ->for($serviceMonitoringTarget)
                ->state(['succeeded' => $succeeded, 'created_at' => now()->subHours($hoursAgo)])
                ->create();
        }

        return $serviceMonitoringTarget;
    });

    $response = getServiceMonitorStatuses(['sort' => $sort, 'direction' => $direction])->assertOk();

    expect(collect($response->json('data'))->pluck('name')->all())->toBe($expectedOrder);
})->with([
    'name ascending' => ['name', 'asc', ['Alpha', 'Bravo', 'Charlie', 'Delta']],
    'name descending' => ['name', 'desc', ['Delta', 'Charlie', 'Bravo', 'Alpha']],
    'status ascending' => ['status', 'asc', ['Delta', 'Alpha', 'Bravo', 'Charlie']],
    'status descending' => ['status', 'desc', ['Charlie', 'Bravo', 'Alpha', 'Delta']],
    '30-day uptime ascending' => ['thirty_day_uptime', 'asc', ['Charlie', 'Bravo', 'Alpha', 'Delta']],
    '30-day uptime descending' => ['thirty_day_uptime', 'desc', ['Alpha', 'Bravo', 'Charlie', 'Delta']],
    '12-month uptime ascending' => ['twelve_month_uptime', 'asc', ['Alpha', 'Bravo', 'Charlie', 'Delta']],
    '12-month uptime descending' => ['twelve_month_uptime', 'desc', ['Bravo', 'Alpha', 'Charlie', 'Delta']],
    'last checked ascending' => ['last_checked_at', 'asc', ['Charlie', 'Alpha', 'Bravo', 'Delta']],
    'last checked descending' => ['last_checked_at', 'desc', ['Alpha', 'Bravo', 'Charlie', 'Delta']],
    'frequency ascending' => ['frequency', 'asc', ['Bravo', 'Delta', 'Alpha', 'Charlie']],
    'frequency descending' => ['frequency', 'desc', ['Charlie', 'Alpha', 'Delta', 'Bravo']],
]);

it('includes the uptime of each monitor whether or not the list is sorted by it', function (string $sort) {
    freezeTime();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subDays(29)->subHours(12)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()->subHour()]);

    getServiceMonitorStatuses(['sort' => $sort])
        ->assertOk()
        ->assertJsonPath('data.0.thirty_day_uptime_percentage', 50)
        ->assertJsonPath('data.0.twelve_month_uptime_percentage', null);
})->with(['name', 'status', 'thirty_day_uptime', 'twelve_month_uptime']);

it('validates the inputs', function (array $query, string $error) {
    getServiceMonitorStatuses($query)->assertJsonValidationErrors($error);
})->with([
    'search string' => [['search' => ['portal']], 'search'],
    'sort enum' => [['sort' => 'domain'], 'sort'],
    'direction in' => [['direction' => 'sideways'], 'direction'],
    'timezone max' => [['timezone' => str_repeat('a', 256)], 'timezone'],
]);

it('does not reject a timezone the date extension cannot resolve by name', function () {
    getServiceMonitorStatuses(['timezone' => 'Asia/Calcutta'])->assertOk();
});

describe('authorization', function () {
    it('requires contact authentication', function () {
        $settings = app(PortalSettings::class);
        $settings->knowledge_management_portal_enabled = true;
        $settings->save();

        getJson(URL::route('api.portal.status', absolute: false))->assertUnauthorized();
    });

    it('denies access without the service monitoring add-on', function () {
        $licenseSettings = app(LicenseSettings::class);
        $licenseSettings->data->addons->serviceMonitoring = false;
        $licenseSettings->save();

        getServiceMonitorStatuses()->assertForbidden();
    });
});
