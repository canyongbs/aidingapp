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
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Settings\LicenseSettings;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeTime;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $this->contact = Contact::factory()->create();

    actingAs($this->contact);
});

it('displays the service monitor data', function () {
    freezeTime();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'name' => 'Customer Website',
        'description' => 'Monitors the public website via HTTPS',
        'domain' => 'https://www.example.com',
        'monitor_type' => MonitorType::Availability,
        'frequency' => ServiceMonitoringFrequency::FiveMinutes,
    ]);

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subDays(6)->subHours(12)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subDays(2)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()->subHours(3)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subMinutes(2)]);

    getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget]))
        ->assertOk()
        ->assertJsonPath('data.id', $serviceMonitoringTarget->getKey())
        ->assertJsonPath('data.name', 'Customer Website')
        ->assertJsonPath('data.description', 'Monitors the public website via HTTPS')
        ->assertJsonPath('data.domain', 'https://www.example.com')
        ->assertJsonPath('data.monitor_type', 'availability')
        ->assertJsonPath('data.monitor_type_label', 'Availability')
        ->assertJsonPath('data.frequency_label', '5 minutes')
        ->assertJsonPath('data.status', 'degraded')
        ->assertJsonPath('data.last_checked_at', now()->subMinutes(2)->toIso8601String())
        ->assertJsonPath('data.uptime_percentages', [
            'twenty_four_hours' => 50,
            'seven_days' => 75,
            'thirty_days' => null,
            'ninety_days' => null,
            'twelve_months' => null,
        ])
        ->assertJsonPath('data.history_period', 'past_month')
        ->assertJsonCount(30, 'data.history')
        ->assertJsonPath('data.history.29.status', 'degraded')
        ->assertJsonPath('data.history.29.checks_count', 2);
});

it('can show the history for a different period', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()]);

    getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget, 'period' => 'past_hour']))
        ->assertOk()
        ->assertJsonPath('data.history_period', 'past_hour')
        ->assertJsonCount(60, 'data.history')
        ->assertJsonPath('data.history.59.status', 'outage');
});

it('validates the inputs', function (array $query, string $error) {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget, ...$query]))
        ->assertJsonValidationErrors($error);
})->with([
    'period enum' => [['period' => 'past_decade'], 'period'],
    'timezone max' => [['timezone' => str_repeat('a', 256)], 'timezone'],
]);

it('does not reject a timezone the date extension cannot resolve by name', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget, 'timezone' => 'Asia/Calcutta']))
        ->assertOk();
});

it('does not show a service monitor that does not exist', function () {
    getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => (string) Str::uuid()]))
        ->assertNotFound();
});

describe('authorization', function () {
    it('requires contact authentication', function () {
        auth()->forgetGuards();

        $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

        getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget]))
            ->assertUnauthorized();
    });

    it('does not show a confidential service monitor unless the contact is granted access', function () {
        $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->confidential()->create();

        getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget]))
            ->assertNotFound();

        $serviceMonitoringTarget->confidentialContacts()->attach($this->contact);

        getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget]))
            ->assertOk();
    });

    it('denies access without the service monitoring add-on', function () {
        $licenseSettings = app(LicenseSettings::class);
        $licenseSettings->data->addons->serviceMonitoring = false;
        $licenseSettings->save();

        $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

        getJson(route('api.portal.status.show', ['serviceMonitoringTarget' => $serviceMonitoringTarget]))
            ->assertForbidden();
    });
});
