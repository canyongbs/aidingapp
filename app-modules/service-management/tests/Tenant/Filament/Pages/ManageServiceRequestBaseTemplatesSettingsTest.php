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
use App\Filament\Clusters\GlobalServiceManagementCluster\Pages\ManageServiceRequestBaseTemplatesSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('it prevents access when the user does not have permission', function () {
    actingAs(User::factory()->create());

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertForbidden();
});

test('it allows access for super admins', function () {
    asSuperAdmin();

    get(ManageServiceRequestBaseTemplatesSettings::getUrl())
        ->assertSuccessful();
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
