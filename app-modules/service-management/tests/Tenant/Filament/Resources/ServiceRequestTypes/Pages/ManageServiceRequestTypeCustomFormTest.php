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

use AidingApp\Form\Enums\Rounding;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ManageServiceRequestTypeCustomForm;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->data->addons->onlineForms = true;
    $settings->save();
});

it('is not accessible when the online forms feature is disabled', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = false;
    $settings->save();

    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    expect(ManageServiceRequestTypeCustomForm::canAccess(['record' => $type]))->toBeFalse();
});

it('is forbidden for users without the required permissions', function () {
    $user = User::factory()->create();
    $type = ServiceRequestType::factory()->create();

    actingAs($user);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])->assertForbidden();
});

it('creates a form for a type that does not have one yet', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    expect($type->form)->toBeNull();

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent())
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');

    expect($type->form)->not->toBeNull()
        ->and($type->form->fields()->count())->toBe(1);
});

it('edits the form in place when it has no submissions', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()->for($type, 'type')->create();

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('Updated label'))
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');

    expect($type->form->getKey())->toBe($form->getKey())
        ->and(ServiceRequestForm::onlyArchived()->whereBelongsTo($type, 'type')->exists())->toBeFalse()
        ->and($type->form->fields()->where('label', 'Updated label')->exists())->toBeTrue();
});

it('archives the current form and creates a new version when submissions exist', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()->for($type, 'type')->create();

    $form->submissions()->create(['submitted_at' => now()]);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('New version field'))
        ->call('save')
        ->assertHasNoFormErrors();

    $form->refresh();
    $type->unsetRelation('form');

    expect($form->isArchived())->toBeTrue()
        ->and($form->trashed())->toBeFalse()
        ->and($type->form)->not->toBeNull()
        ->and($type->form->getKey())->not->toBe($form->getKey())
        ->and($type->form->fields()->where('label', 'New version field')->exists())->toBeTrue();
});

it('creates a new version when a non-submitted submission exists so its field references are preserved', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()->for($type, 'type')->create();

    // A submission that was never submitted (e.g. canceled) still references the form's fields.
    $form->submissions()->create(['canceled_at' => now()]);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('New version field'))
        ->call('save')
        ->assertHasNoFormErrors();

    $form->refresh();
    $type->unsetRelation('form');

    expect($form->isArchived())->toBeTrue()
        ->and($type->form->getKey())->not->toBe($form->getKey());
});

it('only ever has one active form per type after repeated edits with submissions', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    foreach (['v1', 'v2', 'v3'] as $label) {
        livewire(ManageServiceRequestTypeCustomForm::class, [
            'record' => $type->getKey(),
        ])
            ->set('data.is_wizard', false)
            ->set('data.content', customFormContent($label))
            ->call('save')
            ->assertHasNoFormErrors();

        $type->unsetRelation('form');

        // Simulate a submission against each version so the next save creates a new version.
        $type->form->submissions()->create(['submitted_at' => now()]);
    }

    expect(
        ServiceRequestForm::query()->withoutArchived()->whereBelongsTo($type, 'type')->count()
    )->toBe(1);
});

it('gives each new version a distinct name so it does not collide with the archived one', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create(['name' => 'Password Reset']);

    // First save creates the form through the page, so its name is derived from the type.
    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('Email'))
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');
    $firstName = $type->form->name;

    // A submission forces the next save to create a new version (which keeps the same type).
    $type->form->submissions()->create(['submitted_at' => now()]);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('Phone'))
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');

    expect($firstName)->toBe('Password Reset Form')
        ->and($type->form->name)->not->toBe($firstName)
        ->and($type->form->name)->toBe('Password Reset Form (2)');
});

it('keeps prior submissions rendering against their archived form version', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->state(['content' => customFormContent('Old field')])
        ->create();

    $submission = $form->submissions()->create(['submitted_at' => now()]);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('Brand new field'))
        ->call('save')
        ->assertHasNoFormErrors();

    $submission->refresh();

    expect($submission->submissible)->not->toBeNull()
        ->and($submission->submissible->getKey())->toBe($form->getKey())
        ->and($submission->submissible->isArchived())->toBeTrue();
});

it('preserves the field responses of a prior submission after a version bump', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('Old field'))
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');
    $oldForm = $type->form;
    $oldField = $oldForm->fields()->firstOrFail();

    $submission = $oldForm->submissions()->create(['submitted_at' => now()]);
    $submission->fields()->attach($oldField->getKey(), [
        'id' => Str::orderedUuid(),
        'response' => 'Jane Doe',
    ]);

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent('New field'))
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');
    $submission->refresh();

    expect($oldForm->fresh()->isArchived())->toBeTrue()
        ->and($type->form->getKey())->not->toBe($oldForm->getKey())
        ->and(ServiceRequestFormField::find($oldField->getKey()))->not->toBeNull()
        ->and($submission->submissible->getKey())->toBe($oldForm->getKey())
        ->and($submission->fields()->first()->getKey())->toBe($oldField->getKey())
        ->and($submission->fields()->first()->pivot->response)->toBe('Jane Doe');
});

it('saves the description and appearance color and rounding settings', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.description', 'How to request access.')
        ->set('data.is_wizard', false)
        ->set('data.content', customFormContent())
        ->set('data.primary_color', 'rose')
        ->set('data.rounding', Rounding::Full->value)
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');

    expect($type->form->description)->toBe('How to request access.')
        ->and($type->form->primary_color)->toBe('rose')
        ->and($type->form->rounding)->toBe(Rounding::Full);
});

it('stores steps and clears top-level content for a multi-step form', function () {
    asSuperAdmin();

    $type = ServiceRequestType::factory()->create();

    livewire(ManageServiceRequestTypeCustomForm::class, [
        'record' => $type->getKey(),
    ])
        ->set('data.is_wizard', true)
        ->set('data.steps', [
            [
                'label' => 'Step One',
                'content' => customFormContent('Step field'),
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $type->unsetRelation('form');

    expect($type->form->is_wizard)->toBeTrue()
        ->and($type->form->content)->toBeNull()
        ->and($type->form->steps()->count())->toBe(1)
        ->and($type->form->steps()->first()->label)->toBe('Step One');
});

function customFormContent(string $label = 'Your name'): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'customBlock',
                'attrs' => [
                    'id' => TextInputFormFieldBlock::type(),
                    'config' => [
                        'label' => $label,
                        'isRequired' => true,
                    ],
                ],
            ],
        ],
    ];
}
