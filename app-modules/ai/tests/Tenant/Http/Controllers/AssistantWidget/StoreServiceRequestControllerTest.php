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
use AidingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();

    ServiceRequestStatus::firstOrCreate(
        ['name' => 'New'],
        ServiceRequestStatus::factory()->open()->systemProtected()->make()->getAttributes(),
    );
});

function postAssistantServiceRequest(Contact $contact, ServiceRequestType $type, array $data)
{
    $contact->createToken('assistant-widget-access-token');

    return actingAs($contact, 'contact')->postJson(
        route('widgets.assistant.api.service-request.store', ['type' => $type]),
        $data,
        ['Origin' => config('app.url')],
    );
}

it('assigns the configured default priority when no priority is submitted', function () {
    $type = ServiceRequestType::factory()->create();
    $defaultPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $defaultPriority->getKey()]);
    $contact = Contact::factory()->create();

    $response = postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant priority test',
        'description' => 'Configured priority should be assigned.',
    ]);

    $response->assertOk();

    assertDatabaseHas(ServiceRequest::class, [
        'priority_id' => $defaultPriority->getKey(),
        'respondent_id' => $contact->getKey(),
    ]);
});

it('prohibits a submitted priority when a default priority is configured', function () {
    $type = ServiceRequestType::factory()->create();
    $defaultPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $submittedPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $defaultPriority->getKey()]);
    $contact = Contact::factory()->create();

    $response = postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant priority test',
        'description' => 'Configured priority should be enforced.',
        'priority_id' => $submittedPriority->getKey(),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('priority_id');

    expect(ServiceRequest::query()->count())->toBe(0);
});

it('requires a priority when no default priority is configured', function () {
    $type = ServiceRequestType::factory()->create();
    $contact = Contact::factory()->create();

    $response = postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant priority test',
        'description' => 'A priority is required.',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('priority_id');

    expect(ServiceRequest::query()->count())->toBe(0);
});

it('assigns the submitted priority when no default priority is configured', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $contact = Contact::factory()->create();

    $response = postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant priority test',
        'description' => 'Submitted priority should be assigned.',
        'priority_id' => $priority->getKey(),
    ]);

    $response->assertOk();

    assertDatabaseHas(ServiceRequest::class, [
        'priority_id' => $priority->getKey(),
        'respondent_id' => $contact->getKey(),
    ]);
});

it('requires password fields in assistant submissions', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $form = ServiceRequestForm::factory()->wizard()->for($type, 'type')->create();
    $step = new ServiceRequestFormStep(['label' => 'Details', 'sort' => 1]);
    $step->submissible()->associate($form);
    $step->save();
    $field = new ServiceRequestFormField([
        'label' => 'Private credential',
        'type' => PasswordFormFieldBlock::type(),
        'is_required' => true,
        'config' => [],
    ]);
    $field->submissible()->associate($form);
    $field->step()->associate($step);
    $field->save();
    $contact = Contact::factory()->create();

    postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant password test',
        'description' => 'Password fields are required.',
        'custom_fields' => [],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field->getKey());

    expect(ServiceRequest::query()->exists())->toBeFalse();
});

it('securely attaches password fields submitted by assistant clients', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $form = ServiceRequestForm::factory()->wizard()->for($type, 'type')->create();
    $step = new ServiceRequestFormStep(['label' => 'Details', 'sort' => 1]);
    $step->submissible()->associate($form);
    $step->save();
    $field = new ServiceRequestFormField([
        'label' => 'Private credential',
        'type' => PasswordFormFieldBlock::type(),
        'is_required' => true,
        'config' => [],
    ]);
    $field->submissible()->associate($form);
    $field->step()->associate($step);
    $field->save();
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create([
        'value' => Crypt::encryptString('service-request-password'),
    ]);

    postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant password test',
        'description' => 'Password fields are supported.',
        'custom_fields' => [
            $field->getKey() => $secret->getKey(),
        ],
    ])->assertOk();

    $serviceRequest = ServiceRequest::query()->sole();
    $submittedField = $serviceRequest->serviceRequestFormSubmission->fields()->whereKey($field)->firstOrFail();
    $secret->refresh();

    expect($secret->related->is($serviceRequest))->toBeTrue()
        ->and($submittedField->pivot->response)->toBe($secret->getKey())
        ->and(app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest)->decryptString($secret->value))
        ->toBe('service-request-password');
});

it('rejects password fields owned by another contact', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $form = ServiceRequestForm::factory()->wizard()->for($type, 'type')->create();
    $step = new ServiceRequestFormStep(['label' => 'Details', 'sort' => 1]);
    $step->submissible()->associate($form);
    $step->save();
    $field = new ServiceRequestFormField([
        'label' => 'Private credential',
        'type' => PasswordFormFieldBlock::type(),
        'is_required' => true,
        'config' => [],
    ]);
    $field->submissible()->associate($form);
    $field->step()->associate($step);
    $field->save();
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for(Contact::factory(), 'author')->create();

    postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant password test',
        'description' => 'Password ownership is enforced.',
        'custom_fields' => [
            $field->getKey() => $secret->getKey(),
        ],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('passwords');

    expect(ServiceRequest::query()->exists())->toBeFalse()
        ->and($secret->refresh()->related_id)->toBeNull();
});
