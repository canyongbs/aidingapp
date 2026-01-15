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
use AidingApp\ServiceManagement\Jobs\AutoSubmitStaleDraftServiceRequests;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\PortalAssistantServiceRequestFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

afterEach(function () {
    PortalAssistantServiceRequestFeature::deactivate();
});

it('does nothing when feature is disabled', function () {
    Queue::fake();

    $draft = createValidStaleDraft();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does nothing when no drafts exist', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for non-draft service requests', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    ServiceRequest::factory()->create([
        'is_draft' => false,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for recently updated drafts', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with null title', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => null,
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with empty title', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => '',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with null description', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => null,
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with empty description', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    travelTo(now()->subHours(2));

    ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => '',
        'priority_id' => $priority->getKey(),
    ]);

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with recently updated form submission', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

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

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with recently updated form field submissions', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

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

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('does not dispatch job for drafts with recently added service request updates', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

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

    $draft->serviceRequestUpdates()->createQuietly([
        'update' => 'Test update',
        'internal' => false,
        'created_by_id' => $draft->respondent_id,
        'created_by_type' => (new Contact())->getMorphClass(),
    ]);

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertNotPushed(AutoSubmitStaleDraftServiceRequest::class);
});

it('dispatches job for valid stale drafts', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $draft = createValidStaleDraft();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertPushed(AutoSubmitStaleDraftServiceRequest::class, function ($job) use ($draft) {
        return $job->uniqueId() === $draft->getKey();
    });
});

it('dispatches jobs for multiple valid stale drafts', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

    $draft1 = createValidStaleDraft();
    $draft2 = createValidStaleDraft();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertPushed(AutoSubmitStaleDraftServiceRequest::class, 2);
});

it('dispatches job for stale draft with old form submission', function () {
    PortalAssistantServiceRequestFeature::activate();
    Queue::fake();

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

    $submission = $form->submissions()->create();

    $draft->service_request_form_submission_id = $submission->getKey();
    $draft->saveQuietly();

    travelBack();

    (new AutoSubmitStaleDraftServiceRequests())->handle();

    Queue::assertPushed(AutoSubmitStaleDraftServiceRequest::class, function ($job) use ($draft) {
        return $job->uniqueId() === $draft->getKey();
    });
});

function createValidStaleDraft(): ServiceRequest
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
