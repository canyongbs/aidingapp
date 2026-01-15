<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Jobs\AutoSubmitStaleDraftServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\PortalAssistantServiceRequestFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

beforeEach(function () {
    PortalAssistantServiceRequestFeature::activate();
});

afterEach(function () {
    PortalAssistantServiceRequestFeature::deactivate();
});

it('does nothing when service request does not exist', function () {
    $nonExistentId = Str::uuid()->toString();

    (new AutoSubmitStaleDraftServiceRequest($nonExistentId))->handle();

    expect(true)->toBeTrue();
});

it('does nothing when service request is not a draft', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $serviceRequest = ServiceRequest::factory()->create([
        'is_draft' => false,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    $originalNumber = $serviceRequest->service_request_number;

    (new AutoSubmitStaleDraftServiceRequest($serviceRequest->getKey()))->handle();

    $serviceRequest->refresh();

    expect($serviceRequest->is_draft)->toBeFalse()
        ->and($serviceRequest->service_request_number)->toBe($originalNumber);
});

it('does nothing when draft was recently updated', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when form submission was recently updated', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when form fields were recently updated', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $field = $form->fields()->create([
        'label' => 'Test Field',
        'type' => 'text_input',
        'is_required' => false,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    travelBack();

    DB::table('service_request_form_field_submission')->insert([
        'id' => Str::uuid()->toString(),
        'service_request_form_submission_id' => $submission->getKey(),
        'service_request_form_field_id' => $field->getKey(),
        'response' => 'Test response',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when service request updates were recently added', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    $draft->serviceRequestUpdates()->create([
        'update' => 'Test update',
        'internal' => false,
        'created_by_id' => $draft->respondent_id,
        'created_by_type' => (new Contact())->getMorphClass(),
    ]);

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when title is missing', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => null,
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when description is missing', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => null,
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when description is empty string', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => '',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when required form fields are missing and no submission exists', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $form->fields()->create([
        'label' => 'Required Field',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when required form fields are not filled', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $form->fields()->create([
        'label' => 'Required Field',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('does nothing when required form field has empty response', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $field = $form->fields()->create([
        'label' => 'Required Field',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    DB::table('service_request_form_field_submission')->insert([
        'id' => Str::uuid()->toString(),
        'service_request_form_submission_id' => $submission->getKey(),
        'service_request_form_field_id' => $field->getKey(),
        'response' => '',
        'created_at' => now()->subHours(2),
        'updated_at' => now()->subHours(2),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeTrue();
});

it('submits draft when all conditions are met with no form', function () {
    $draft = createStaleDraft();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeFalse()
        ->and($draft->service_request_number)->not->toBeNull()
        ->and($draft->status)->not->toBeNull();
});

it('submits draft when form has only optional fields', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $form->fields()->create([
        'label' => 'Optional Field',
        'type' => 'text_input',
        'is_required' => false,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeFalse();
});

it('submits draft when all required form fields are filled', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $form = $type->form()->create([
        'name' => 'Test Form',
    ]);

    $requiredField = $form->fields()->create([
        'label' => 'Required Field',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    $form->fields()->create([
        'label' => 'Optional Field',
        'type' => 'text_input',
        'is_required' => false,
        'config' => [],
    ]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    DB::table('service_request_form_field_submission')->insert([
        'id' => Str::uuid()->toString(),
        'service_request_form_submission_id' => $submission->getKey(),
        'service_request_form_field_id' => $requiredField->getKey(),
        'response' => 'Filled response',
        'created_at' => now()->subHours(2),
        'updated_at' => now()->subHours(2),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequest($draft->getKey()))->handle();

    $draft->refresh();

    expect($draft->is_draft)->toBeFalse()
        ->and($draft->service_request_number)->not->toBeNull()
        ->and($draft->status)->not->toBeNull();
});

function createStaleDraft(): ServiceRequest
{
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    $draft = ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    return $draft;
}
