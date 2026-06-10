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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ServiceRequestTypeEmailTemplatePage;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('fill with AI action is hidden when settings are disabled', function () {
    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->is_enabled = false;
    $settings->save();

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => 'manager',
    ]);

    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(ServiceRequestTypeEmailTemplatePage::class, [
        'record' => $serviceRequestType->getKey(),
        'type' => ServiceRequestEmailTemplateType::Created,
    ])
        ->assertActionHidden('fillWithAi');
});

test('fill with AI action is disabled when no AI templates exist for the event type', function () {
    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(ServiceRequestTypeEmailTemplatePage::class, [
        'record' => $serviceRequestType->getKey(),
        'type' => ServiceRequestEmailTemplateType::Created,
    ])
        ->assertActionVisible('fillWithAi')
        ->assertActionDisabled('fillWithAi');
});

test('fill with AI action is enabled when feature is enabled and AI templates exist', function () {
    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->is_enabled = true;
    $settings->save();

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => 'manager',
    ]);

    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(ServiceRequestTypeEmailTemplatePage::class, [
        'record' => $serviceRequestType->getKey(),
        'type' => ServiceRequestEmailTemplateType::Created,
    ])
        ->assertActionVisible('fillWithAi')
        ->assertActionEnabled('fillWithAi');
});
