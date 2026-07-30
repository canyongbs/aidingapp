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
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('it prevents access when the user does not have permission', function () {
    actingAs(User::factory()->create());

    get(ManageServiceRequestNotificationAutomationSettings::getUrl())
        ->assertForbidden();
});

test('it allows access for super admins', function () {
    asSuperAdmin();

    get(ManageServiceRequestNotificationAutomationSettings::getUrl())
        ->assertSuccessful();
});

test('it disables the unsaved data changes alert to avoid false positives on this complex form', function () {
    $page = new ManageServiceRequestNotificationAutomationSettings();

    $hasUnsavedDataChangesAlert = (new ReflectionClass($page))
        ->getProperty('hasUnsavedDataChangesAlert')
        ->getValue($page);

    expect($hasUnsavedDataChangesAlert)->toBeFalse();
});

test('it saves is_enabled, ai_prompt, and ai instructions without touching the example subject and body', function () {
    asSuperAdmin();

    $existing = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'ai_instructions' => 'Old instructions.',
    ]);

    $originalSubject = $existing->subject;
    $originalBody = $existing->body;

    $aiPrompt = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Custom AI prompt.'],
                ],
            ],
        ],
    ];

    livewire(ManageServiceRequestNotificationAutomationSettings::class)
        ->fillForm([
            'is_enabled' => true,
            'ai_prompt' => $aiPrompt,
            "templates.{$existing->type->value}.{$existing->role->value}.ai_instructions" => 'Updated instructions.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $existing->refresh();

    expect($existing->ai_instructions)->toBe('Updated instructions.');
    expect($existing->subject)->toEqual($originalSubject);
    expect($existing->body)->toEqual($originalBody);

    $settings = app(ServiceRequestNotificationAutomationSettings::class);

    expect($settings->is_enabled)->toBeTrue();
    expect($settings->ai_prompt)->toEqual($aiPrompt);
});

test('it leaves existing templates untouched when saving without changes', function () {
    asSuperAdmin();

    $existing = collect([
        [ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, 'Keep it friendly.'],
        [ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, null],
        [ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, 'Be brief.'],
    ])->map(fn (array $attributes) => ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => $attributes[0],
        'role' => $attributes[1],
        'ai_instructions' => $attributes[2],
    ]));

    livewire(ManageServiceRequestNotificationAutomationSettings::class)
        ->fillForm(['is_enabled' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestNotificationAutomationEmailTemplate::count())->toBe(3);

    foreach ($existing as $template) {
        $fresh = ServiceRequestNotificationAutomationEmailTemplate::query()->findOrFail($template->getKey());

        expect($fresh->subject)->toEqual($template->subject);
        expect($fresh->body)->toEqual($template->body);
        expect($fresh->ai_instructions)->toBe($template->ai_instructions);
    }
});

test('it does not create empty templates when no ai instructions have been filled in', function () {
    asSuperAdmin();

    livewire(ManageServiceRequestNotificationAutomationSettings::class)
        ->fillForm(['is_enabled' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(ServiceRequestNotificationAutomationEmailTemplate::query()->exists())->toBeFalse();
});

test('it keeps a template with an example subject and body when the ai instructions are cleared', function () {
    asSuperAdmin();

    $existing = ServiceRequestNotificationAutomationEmailTemplate::factory()->create([
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'ai_instructions' => 'Old instructions.',
    ]);

    $originalSubject = $existing->subject;
    $originalBody = $existing->body;

    livewire(ManageServiceRequestNotificationAutomationSettings::class)
        ->fillForm([
            'is_enabled' => true,
            "templates.{$existing->type->value}.{$existing->role->value}.ai_instructions" => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $existing->refresh();

    expect($existing->ai_instructions)->toBeNull();
    expect($existing->subject)->toEqual($originalSubject);
    expect($existing->body)->toEqual($originalBody);
});
