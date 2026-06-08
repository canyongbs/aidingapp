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

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\EditServiceRequestTypeNotifications;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function allNotificationSettings(bool $enabled = false): array
{
    $settings = [];

    $surveyHiddenRoleSlugs = ['managers', 'auditors', 'assigned_managers'];

    foreach (ServiceRequestEmailTemplateType::cases() as $templateType) {
        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            $roleSlug = $role->value . 's';
            $eventSlug = $templateType->getEventSlug();
            $isSurveyResponse = $templateType === ServiceRequestEmailTemplateType::SurveyResponse;

            foreach (ServiceRequestNotificationChannel::cases() as $channel) {
                if ($isSurveyResponse && in_array($roleSlug, $surveyHiddenRoleSlugs)) {
                    continue;
                }

                if (
                    $isSurveyResponse
                    && $role === ServiceRequestTypeEmailTemplateRole::Customer
                    && $channel === ServiceRequestNotificationChannel::Notification
                ) {
                    continue;
                }

                $settings["is_{$roleSlug}_{$eventSlug}_{$channel->value}_enabled"] = $enabled;
            }
        }
    }

    return $settings;
}

/**
 * Counts how many preference rows should exist when all visible checkboxes are saved.
 * SurveyResponse contributes exactly 1 row (Customer × Email); all other SurveyResponse
 * combinations are excluded by the blade $shouldShow logic.
 */
function expectedTotalPreferenceCount(): int
{
    $roles = count(ServiceRequestTypeEmailTemplateRole::cases());
    $nonSurveyEvents = count(ServiceRequestEmailTemplateType::cases()) - 1;
    $channels = count(ServiceRequestNotificationChannel::cases());
    $nonSurveyTotal = $roles * $nonSurveyEvents * $channels;
    $surveyTotal = 1;

    return $nonSurveyTotal + $surveyTotal;
}

test('A successful action on the EditServiceRequestTypeNotifications page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            EditServiceRequestTypeNotifications::getUrl([
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('form is pre-filled with enabled preferences loaded from the pivot table', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Created,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Manager,
        'notification_channel' => ServiceRequestNotificationChannel::Email,
        'is_enabled' => true,
    ]);

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Closed,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'notification_channel' => ServiceRequestNotificationChannel::Notification,
        'is_enabled' => false,
    ]);

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertSet('data.settings.is_managers_service_request_created_email_enabled', true)
        ->assertSet('data.settings.is_customers_service_request_closed_notification_enabled', false);
});

test('form is pre-filled with assigned manager preferences loaded from the pivot table', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Assigned,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        'notification_channel' => ServiceRequestNotificationChannel::Email,
        'is_enabled' => true,
    ]);

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertSet('data.settings.is_assigned_managers_service_request_assigned_email_enabled', true);
});

test('saving creates a new preference row for a single combination', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'settings' => [
                'is_managers_service_request_created_email_enabled' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Created->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Manager->value,
        'notification_channel' => ServiceRequestNotificationChannel::Email->value,
        'is_enabled' => true,
    ]);
});

test('saving upserts all visible matrix combinations into the pivot table', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm(['settings' => allNotificationSettings(enabled: true)])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount('service_request_type_email_preference', expectedTotalPreferenceCount());

    $surveyHiddenRoleSlugs = ['managers', 'auditors', 'assigned_managers'];

    foreach (ServiceRequestEmailTemplateType::cases() as $templateType) {
        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            $roleSlug = $role->value . 's';
            $isSurveyResponse = $templateType === ServiceRequestEmailTemplateType::SurveyResponse;

            foreach (ServiceRequestNotificationChannel::cases() as $channel) {
                if ($isSurveyResponse && in_array($roleSlug, $surveyHiddenRoleSlugs)) {
                    continue;
                }

                if (
                    $isSurveyResponse
                    && $role === ServiceRequestTypeEmailTemplateRole::Customer
                    && $channel === ServiceRequestNotificationChannel::Notification
                ) {
                    continue;
                }

                assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
                    'service_request_type_id' => $serviceRequestType->getKey(),
                    'service_request_email_template_type' => $templateType->value,
                    'service_request_email_template_role' => $role->value,
                    'notification_channel' => $channel->value,
                    'is_enabled' => true,
                ]);
            }
        }
    }
});

test('saving updates an existing enabled preference to disabled', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Update,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        'notification_channel' => ServiceRequestNotificationChannel::Notification,
        'is_enabled' => true,
    ]);

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'settings' => [
                'is_auditors_service_request_update_notification_enabled' => false,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Update->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Auditor->value,
        'notification_channel' => ServiceRequestNotificationChannel::Notification->value,
        'is_enabled' => false,
    ]);
});

test('saving updates an existing disabled preference to enabled', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::StatusChange,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        'notification_channel' => ServiceRequestNotificationChannel::Email,
        'is_enabled' => false,
    ]);

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'settings' => [
                'is_assigned_managers_service_request_status_change_email_enabled' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::StatusChange->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::AssignedManager->value,
        'notification_channel' => ServiceRequestNotificationChannel::Email->value,
        'is_enabled' => true,
    ]);
});

test('survey response email preference is only stored for the customer role', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm(['settings' => allNotificationSettings(enabled: true)])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::SurveyResponse->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Customer->value,
        'notification_channel' => ServiceRequestNotificationChannel::Email->value,
        'is_enabled' => true,
    ]);

    $hiddenRoles = [
        ServiceRequestTypeEmailTemplateRole::Manager,
        ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ServiceRequestTypeEmailTemplateRole::Auditor,
    ];

    foreach ($hiddenRoles as $role) {
        foreach (ServiceRequestNotificationChannel::cases() as $channel) {
            assertDatabaseMissing(ServiceRequestTypeEmailPreference::class, [
                'service_request_type_id' => $serviceRequestType->getKey(),
                'service_request_email_template_type' => ServiceRequestEmailTemplateType::SurveyResponse->value,
                'service_request_email_template_role' => $role->value,
            ]);
        }
    }
});

test('survey response notification channel is never written to the pivot table for any role', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm(['settings' => allNotificationSettings(enabled: true)])
        ->call('save')
        ->assertHasNoFormErrors();

    foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
        assertDatabaseMissing(ServiceRequestTypeEmailPreference::class, [
            'service_request_type_id' => $serviceRequestType->getKey(),
            'service_request_email_template_type' => ServiceRequestEmailTemplateType::SurveyResponse->value,
            'service_request_email_template_role' => $role->value,
            'notification_channel' => ServiceRequestNotificationChannel::Notification->value,
        ]);
    }
});

test('EditServiceRequestTypeNotifications is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeNotifications::getUrl([
                'record' => $serviceRequestType,
            ])
        )
        ->assertForbidden();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            EditServiceRequestTypeNotifications::getUrl([
                'record' => $serviceRequestType,
            ])
        )
        ->assertSuccessful();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'settings' => [
                'is_managers_service_request_created_email_enabled' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Created->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Manager->value,
        'notification_channel' => ServiceRequestNotificationChannel::Email->value,
        'is_enabled' => true,
    ]);
});

test('EditServiceRequestTypeNotifications is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeNotifications::getUrl([
                'record' => $serviceRequestType,
            ])
        )
        ->assertForbidden();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            EditServiceRequestTypeNotifications::getUrl([
                'record' => $serviceRequestType,
            ])
        )
        ->assertSuccessful();

    livewire(EditServiceRequestTypeNotifications::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'settings' => [
                'is_customers_service_request_created_email_enabled' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestTypeEmailPreference::class, [
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::Created->value,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Customer->value,
        'notification_channel' => ServiceRequestNotificationChannel::Email->value,
        'is_enabled' => true,
    ]);
});
