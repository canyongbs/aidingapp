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
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestCustomEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;
use App\Filament\Clusters\ServiceManagementAdministration\Pages\ServiceRequestTypeTemplates;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function serviceRequestTypeTemplatesDoc(string $text): array
{
    return ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]]];
}

test('it prevents access when the user does not have permission', function () {
    actingAs(User::factory()->create());

    get(ServiceRequestTypeTemplates::getUrl())
        ->assertForbidden();
});

test('it allows access for a user with permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);
    get(ServiceRequestTypeTemplates::getUrl())
        ->assertSuccessful();

    asSuperAdmin();

    get(ServiceRequestTypeTemplates::getUrl())
        ->assertSuccessful();
});

test('it saves the override flag and custom template content', function () {
    asSuperAdmin();

    expect(app(ServiceRequestNotificationAutomationSettings::class)->use_custom_templates)->toBeFalse();

    livewire(ServiceRequestTypeTemplates::class)
        ->set('data.use_custom_templates', true)
        ->callMountedAction()
        ->fillForm([
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.subject' => serviceRequestTypeTemplatesDoc('Hello'),
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.body' => serviceRequestTypeTemplatesDoc('Body'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(ServiceRequestNotificationAutomationSettings::class)->use_custom_templates)->toBeTrue();

    $template = ServiceRequestCustomEmailTemplate::query()
        ->where('type', ServiceRequestEmailTemplateType::Created)
        ->where('role', ServiceRequestTypeEmailTemplateRole::Customer)
        ->firstOrFail();

    expect($template->subject)->toEqual(serviceRequestTypeTemplatesDoc('Hello'));
    expect($template->body)->toEqual(serviceRequestTypeTemplatesDoc('Body'));
});

test('it mounts the preload base templates confirmation when the override toggle is turned on', function () {
    asSuperAdmin();

    livewire(ServiceRequestTypeTemplates::class)
        ->set('data.use_custom_templates', true)
        ->assertActionMounted('preloadBaseTemplates');
});

test('it does not mount the preload base templates confirmation when the override toggle is turned off', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->use_custom_templates = true;
    $settings->save();

    livewire(ServiceRequestTypeTemplates::class)
        ->set('data.use_custom_templates', false)
        ->assertActionNotMounted('preloadBaseTemplates');
});

test('confirming the preload base templates action loads the base templates into the form', function () {
    asSuperAdmin();

    $baseTemplate = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'subject' => serviceRequestTypeTemplatesDoc('Base subject'),
        'body' => serviceRequestTypeTemplatesDoc('Base body'),
    ]);

    ServiceRequestCustomEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        'subject' => serviceRequestTypeTemplatesDoc('Stale subject'),
        'body' => serviceRequestTypeTemplatesDoc('Stale body'),
    ]);

    livewire(ServiceRequestTypeTemplates::class)
        ->set('data.use_custom_templates', true)
        ->callMountedAction()
        ->assertHasNoFormErrors()
        ->assertFormSet([
            'use_custom_templates' => true,
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.subject' => $baseTemplate->subject,
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.body' => $baseTemplate->body,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $customerTemplate = ServiceRequestCustomEmailTemplate::query()
        ->where('type', ServiceRequestEmailTemplateType::Created)
        ->where('role', ServiceRequestTypeEmailTemplateRole::Customer)
        ->firstOrFail();

    expect($customerTemplate->subject)->toEqual($baseTemplate->subject);
    expect($customerTemplate->body)->toEqual($baseTemplate->body);

    // the stale manager template has no base equivalent, so overwriting blanks it and it is removed
    expect(ServiceRequestCustomEmailTemplate::query()
        ->where('type', ServiceRequestEmailTemplateType::Created)
        ->where('role', ServiceRequestTypeEmailTemplateRole::Manager)
        ->exists())->toBeFalse();
});

test('cancelling the preload base templates action reverts the override toggle', function () {
    asSuperAdmin();

    livewire(ServiceRequestTypeTemplates::class)
        ->set('data.use_custom_templates', true)
        ->assertActionMounted('preloadBaseTemplates')
        ->mountAction(['preloadBaseTemplates', 'cancel'])
        ->callMountedAction()
        ->assertFormSet([
            'use_custom_templates' => false,
        ]);
});

test('it saves the preload new service request types setting', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    expect($settings->preload_new_service_request_types)->toBeFalse();

    $settings->use_custom_templates = true;
    $settings->save();

    livewire(ServiceRequestTypeTemplates::class)
        ->fillForm([
            'preload_new_service_request_types' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(ServiceRequestNotificationAutomationSettings::class)->preload_new_service_request_types)->toBeTrue();

    livewire(ServiceRequestTypeTemplates::class)
        ->fillForm([
            'preload_new_service_request_types' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(ServiceRequestNotificationAutomationSettings::class)->preload_new_service_request_types)->toBeFalse();
});

test('it deletes a custom template when its subject and body are cleared', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->use_custom_templates = true;
    $settings->save();

    $existing = ServiceRequestCustomEmailTemplate::query()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'subject' => serviceRequestTypeTemplatesDoc('Existing'),
        'body' => serviceRequestTypeTemplatesDoc('Existing body'),
    ]);

    livewire(ServiceRequestTypeTemplates::class)
        ->fillForm([
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.subject' => null,
            'templates.' . ServiceRequestEmailTemplateType::Created->value . '.' . ServiceRequestTypeEmailTemplateRole::Customer->value . '.body' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestCustomEmailTemplate::query()->whereKey($existing->getKey())->exists())->toBeFalse();
});

test('it applies custom templates to all service request types by default', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->use_custom_templates = true;
    $settings->save();

    $customTemplate = ServiceRequestCustomEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $types = [
        ServiceRequestType::factory()->create(),
        ServiceRequestType::factory()->create(),
    ];

    livewire(ServiceRequestTypeTemplates::class)
        ->callAction('applyCustomTemplates')
        ->assertHasNoFormErrors();

    foreach ($types as $type) {
        $template = ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $type->getKey())
            ->where('type', $customTemplate->type)
            ->where('role', $customTemplate->role)
            ->firstOrFail();

        expect($template->subject)->toEqual($customTemplate->subject);
        expect($template->body)->toEqual($customTemplate->body);
    }
});

test('it applies custom templates only to the selected service request types', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->use_custom_templates = true;
    $settings->save();

    $customTemplate = ServiceRequestCustomEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $selectedType = ServiceRequestType::factory()->create();
    $otherType = ServiceRequestType::factory()->create();

    livewire(ServiceRequestTypeTemplates::class)
        ->callAction('applyCustomTemplates', data: [
            'apply_to' => 'select',
            'service_request_type_ids' => [$selectedType->getKey()],
        ])
        ->assertHasNoFormErrors();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $selectedType->getKey())
        ->where('type', $customTemplate->type)
        ->where('role', $customTemplate->role)
        ->exists())->toBeTrue();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $otherType->getKey())
        ->exists())->toBeFalse();
});

test('it warns when there are no custom templates with content to apply', function () {
    asSuperAdmin();

    $settings = app(ServiceRequestNotificationAutomationSettings::class);
    $settings->use_custom_templates = true;
    $settings->save();

    ServiceRequestType::factory()->create();

    livewire(ServiceRequestTypeTemplates::class)
        ->callAction('applyCustomTemplates')
        ->assertNotified('No custom templates to apply');
});
