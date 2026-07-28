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
use AidingApp\ServiceManagement\Filament\Pages\ManageServiceRequestNotificationAutomationSettings;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Filament\Clusters\GlobalServiceManagementCluster\Pages\ManageServiceRequestBaseTemplatesSettings;
use App\Models\Authenticatable;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('it prevents access when the user does not have permission', function () {
    actingAs(User::factory()->create());

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertForbidden();
});

test('it prevents access for ai admins', function () {
    $user = User::factory()->create();
    $user->assignRole(Authenticatable::AI_ADMIN_ROLE);

    actingAs($user);

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertForbidden();
});

test('it allows access for super admins', function () {
    asSuperAdmin();

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertSuccessful();
});

test('it disables the unsaved data changes alert to avoid false positives on this complex form', function () {
    $page = new ManageServiceRequestBaseTemplatesSettings();

    $hasUnsavedDataChangesAlert = (new ReflectionClass($page))
        ->getProperty('hasUnsavedDataChangesAlert')
        ->getValue($page);

    expect($hasUnsavedDataChangesAlert)->toBeFalse();
});

test('it leaves existing templates untouched when saving without changes', function () {
    asSuperAdmin();

    $existing = collect([
        [ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, 'Keep it friendly.'],
        [ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, null],
        [ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, 'Be brief.'],
        [ServiceRequestEmailTemplateType::SurveyResponse, ServiceRequestTypeEmailTemplateRole::Customer, null],
    ])->map(fn (array $attributes) => ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => $attributes[0],
        'role' => $attributes[1],
        'ai_instructions' => $attributes[2],
    ]));

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestNotificationAutomationEmailTemplate::count())->toBe(4);

    foreach ($existing as $template) {
        $fresh = ServiceRequestNotificationAutomationEmailTemplate::query()->findOrFail($template->getKey());

        expect($fresh->subject)->toEqual($template->subject);
        expect($fresh->body)->toEqual($template->body);
        expect($fresh->ai_instructions)->toBe($template->ai_instructions);
    }
});

test('it does not create empty templates when nothing has been filled in', function () {
    asSuperAdmin();

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestNotificationAutomationEmailTemplate::query()->exists())->toBeFalse();
});

test('it deletes a template that is left with no example subject, body, or ai instructions', function () {
    asSuperAdmin();

    $existing = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'ai_instructions' => null,
    ]);

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->fillForm([
            "templates.{$existing->type->value}.{$existing->role->value}.subject" => null,
            "templates.{$existing->type->value}.{$existing->role->value}.body" => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestNotificationAutomationEmailTemplate::query()->whereKey($existing->getKey())->exists())->toBeFalse();
});

test('it keeps a template with ai instructions when the example subject and body are cleared', function () {
    asSuperAdmin();

    $existing = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'ai_instructions' => 'Keep it friendly.',
    ]);

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->fillForm([
            "templates.{$existing->type->value}.{$existing->role->value}.subject" => null,
            "templates.{$existing->type->value}.{$existing->role->value}.body" => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $existing->refresh();

    expect($existing->subject)->toBeNull();
    expect($existing->body)->toBeNull();
    expect($existing->ai_instructions)->toBe('Keep it friendly.');
});

test('it saves the example subject and body without touching ai instructions', function () {
    asSuperAdmin();

    $existing = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'ai_instructions' => 'Keep it friendly.',
    ]);

    $subject = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Example Subject'],
                ],
            ],
        ],
    ];

    $body = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Example Body'],
                ],
            ],
        ],
    ];

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->fillForm([
            "templates.{$existing->type->value}.{$existing->role->value}.subject" => $subject,
            "templates.{$existing->type->value}.{$existing->role->value}.body" => $body,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $existing->refresh();

    expect($existing->subject)->toEqual($subject);
    expect($existing->body)->toEqual($body);
    expect($existing->ai_instructions)->toBe('Keep it friendly.');
});

test('it persists example subject/body and ai instructions correctly when saved from both pages', function () {
    asSuperAdmin();

    $type = ServiceRequestEmailTemplateType::Created;
    $role = ServiceRequestTypeEmailTemplateRole::Customer;

    expect(ServiceRequestNotificationAutomationEmailTemplate::query()
        ->where('type', $type)
        ->where('role', $role)
        ->exists())->toBeFalse();

    $subject = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Example Subject'],
                ],
            ],
        ],
    ];

    $body = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Example Body'],
                ],
            ],
        ],
    ];

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->fillForm([
            "templates.{$type->value}.{$role->value}.subject" => $subject,
            "templates.{$type->value}.{$role->value}.body" => $body,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $template = ServiceRequestNotificationAutomationEmailTemplate::query()
        ->where('type', $type)
        ->where('role', $role)
        ->firstOrFail();

    expect($template->subject)->toEqual($subject);
    expect($template->body)->toEqual($body);
    expect($template->ai_instructions)->toBeNull();

    livewire(ManageServiceRequestNotificationAutomationSettings::class)
        ->fillForm([
            'is_enabled' => true,
            "templates.{$type->value}.{$role->value}.ai_instructions" => 'Be concise and friendly.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $template->refresh();

    expect($template->subject)->toEqual($subject);
    expect($template->body)->toEqual($body);
    expect($template->ai_instructions)->toBe('Be concise and friendly.');
});

test('it applies base templates to all service request types by default', function (int $typeIndex) {
    asSuperAdmin();

    $baseTemplate = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $types = [
        ServiceRequestType::factory()->create(),
        ServiceRequestType::factory()->create(),
    ];

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->callAction('applyBaseTemplates')
        ->assertHasNoFormErrors();

    $template = ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $types[$typeIndex]->getKey())
        ->where('type', $baseTemplate->type)
        ->where('role', $baseTemplate->role)
        ->firstOrFail();

    expect($template->subject)->toEqual($baseTemplate->subject);
    expect($template->body)->toEqual($baseTemplate->body);
})->with([
    'first type' => 0,
    'second type' => 1,
]);

test('it applies base templates only to the selected service request types', function () {
    asSuperAdmin();

    $baseTemplate = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $selectedType = ServiceRequestType::factory()->create();
    $otherType = ServiceRequestType::factory()->create();

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->callAction('applyBaseTemplates', data: [
            'apply_to' => 'select',
            'service_request_type_ids' => [$selectedType->getKey()],
        ])
        ->assertHasNoFormErrors();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $selectedType->getKey())
        ->where('type', $baseTemplate->type)
        ->where('role', $baseTemplate->role)
        ->exists())->toBeTrue();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $otherType->getKey())
        ->exists())->toBeFalse();
});

test('it requires at least one selected service request type when applying to a selection', function () {
    asSuperAdmin();

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->callAction('applyBaseTemplates', data: [
            'apply_to' => 'select',
            'service_request_type_ids' => [],
        ])
        ->assertHasFormErrors(['service_request_type_ids' => ['required']]);
});

test('it warns when there are no base templates with content to apply', function () {
    asSuperAdmin();

    $emptyDoc = [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
        ],
    ];

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'subject' => $emptyDoc,
        'body' => $emptyDoc,
    ]);

    ServiceRequestType::factory()->create();

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->callAction('applyBaseTemplates')
        ->assertNotified('No base templates to apply');

    expect(ServiceRequestTypeEmailTemplate::query()->exists())->toBeFalse();
});

test('it warns when there are no service request types to apply templates to', function () {
    asSuperAdmin();

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    expect(ServiceRequestType::query()->exists())->toBeFalse();

    livewire(ManageServiceRequestBaseTemplatesSettings::class)
        ->callAction('applyBaseTemplates')
        ->assertNotified('No service request types found');

    expect(ServiceRequestTypeEmailTemplate::query()->exists())->toBeFalse();
});

test('it prevents applying base templates when the service management feature is disabled', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $serviceRequestType = ServiceRequestType::factory()->create();

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertForbidden();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $serviceRequestType->getKey())
        ->exists())->toBeFalse();
});

test('it prevents applying base templates for authenticated users who are not super admins', function () {
    actingAs(User::factory()->create());

    ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
    ]);

    $serviceRequestType = ServiceRequestType::factory()->create();

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertForbidden();

    expect(ServiceRequestTypeEmailTemplate::query()
        ->where('service_request_type_id', $serviceRequestType->getKey())
        ->exists())->toBeFalse();
});
