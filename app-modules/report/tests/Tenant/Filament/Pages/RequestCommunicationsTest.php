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

use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Filament\Pages\RequestCommunications;
use AidingApp\Report\Models\ReportUserAccess;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

function grantRequestCommunicationsReportAccess(User $user): void
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::RequestCommunications->value,
        'user_id' => $user->getKey(),
    ]);
}

function richContent(string $content): array
{
    return [
        'type' => 'doc',
        'content' => [[
            'type' => 'paragraph',
            'content' => [[
                'type' => 'text',
                'text' => $content,
            ]],
        ]],
    ];
}

it('displays the selected service request type communication configuration', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);
    grantRequestCommunicationsReportAccess($user);
    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create(['name' => 'Technology Support']);

    $preference = new ServiceRequestTypeEmailPreference();
    $preference->serviceRequestType()->associate($serviceRequestType);
    $preference->service_request_email_template_type = ServiceRequestEmailTemplateType::Created;
    $preference->service_request_email_template_role = ServiceRequestTypeEmailTemplateRole::Manager;
    $preference->notification_channel = ServiceRequestNotificationChannel::Email;
    $preference->is_enabled = true;
    $preference->save();

    ServiceRequestTypeEmailTemplate::factory()->create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        'subject' => richContent('A request has been received'),
        'body' => richContent('The request is ready for review.'),
    ]);

    livewire(RequestCommunications::class)
        ->set('filters.serviceRequestType', $serviceRequestType->getKey())
        ->assertSee('Notifications and Alerts')
        ->assertSet('notificationSettings.is_managers_service_request_created_email_enabled', true)
        ->assertSchemaComponentStateSet('notificationSettings', [
            'is_managers_service_request_created_email_enabled' => true,
        ], 'notificationForm')
        ->assertSet('emailTemplates.created.manager.subject', richContent('A request has been received'))
        ->assertSet('emailTemplates.created.manager.body', richContent('The request is ready for review.'));
});

it('renders Filament email-template tabs for each event type', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);
    grantRequestCommunicationsReportAccess($user);
    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    livewire(RequestCommunications::class)
        ->set('filters.serviceRequestType', $serviceRequestType->getKey())
        ->assertSee('Created')
        ->assertSee('Assigned')
        ->assertSee('Update')
        ->assertSee('Status Change')
        ->assertSee('Closed')
        ->assertSee('Survey Response')
        ->assertSee('All Managers')
        ->assertSee('Assigned Manager')
        ->assertSee('Auditors')
        ->assertSee('Customers');
});

describe('authorization', function () {
    it('denies access when the Service Management addon is disabled', function () {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = false;
        $settings->save();

        actingAs(User::factory()->create(['timezone' => 'UTC']));

        livewire(RequestCommunications::class)->assertForbidden();
    });

    it('denies access without an assignment to the report', function () {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = true;
        $settings->save();

        actingAs(User::factory()->create(['timezone' => 'UTC']));

        livewire(RequestCommunications::class)->assertForbidden();
    });

    it('allows access with an assignment to the report', function () {
        $user = User::factory()->create(['timezone' => 'UTC']);
        grantRequestCommunicationsReportAccess($user);
        actingAs($user);

        livewire(RequestCommunications::class)->assertOk();
    });
});
