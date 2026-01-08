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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Prism\Prism\Tool;
use Throwable;

class SubmitServiceRequestTool extends Tool
{
    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('submit_service_request')
            ->for('Submits the service request with all collected data. Only call after user confirms they want to submit.')
            ->withStringParameter('type_id', 'The service request type UUID')
            ->withStringParameter('priority_id', 'The priority UUID')
            ->withStringParameter('title', 'The service request title')
            ->withStringParameter('description', 'The service request description')
            ->withStringParameter('form_data', 'JSON string of form field data keyed by field_id: {"field_id": "value", ...}. Include all collected field values.')
            ->withStringParameter('questions_answers', 'JSON string of clarifying Q&A pairs: [{"question": "...", "answer": "..."}]')
            ->withStringParameter('ai_resolution', 'JSON string of AI resolution data (optional): {"attempted": bool, "successful": bool, "confidence_score": int, "proposed_answer": "..."}')
            ->using($this);
    }

    public function __invoke(
        string $type_id,
        string $priority_id,
        string $title,
        string $description,
        string $form_data = '{}',
        string $questions_answers = '[]',
        string $ai_resolution = '{}',
    ): string {
        $type = ServiceRequestType::find($type_id);

        if (! $type) {
            return json_encode([
                'success' => false,
                'errors' => ['type_id' => 'Service request type not found.'],
            ]);
        }

        $priority = ServiceRequestPriority::where('type_id', $type_id)->find($priority_id);

        if (! $priority) {
            return json_encode([
                'success' => false,
                'errors' => ['priority_id' => 'Priority not found for this service request type.'],
            ]);
        }

        $contact = $this->thread->author;

        if (! $contact instanceof Contact) {
            return json_encode([
                'success' => false,
                'errors' => ['auth' => 'Unable to identify the user submitting this request.'],
            ]);
        }

        $errors = $this->validateRequired($title, $description);

        if (! empty($errors)) {
            return json_encode([
                'success' => false,
                'errors' => $errors,
            ]);
        }

        $formData = json_decode($form_data, true) ?? [];
        $questionsData = json_decode($questions_answers, true) ?? [];
        $aiResolutionData = json_decode($ai_resolution, true) ?? [];

        DB::beginTransaction();

        try {
            $serviceRequest = $this->createServiceRequest($title, $description, $priority, $contact, $aiResolutionData);

            $this->createFormSubmission($type, $priority, $formData, $serviceRequest);

            $this->storeClarifyingQuestions($questionsData, $serviceRequest, $contact);

            $this->assignServiceRequest($serviceRequest);

            DB::commit();

            return json_encode([
                'success' => true,
                'request_id' => $serviceRequest->getKey(),
                'request_number' => $serviceRequest->service_request_number ?? $serviceRequest->getKey(),
                'message' => 'Service request submitted successfully.',
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();
            report($exception);

            return json_encode([
                'success' => false,
                'errors' => ['system' => 'An error occurred while submitting the service request.'],
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function validateRequired(string $title, string $description): array
    {
        $errors = [];

        if (empty(trim($title))) {
            $errors['title'] = 'Title is required.';
        }

        if (empty(trim($description))) {
            $errors['description'] = 'Description is required.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $aiResolutionData
     */
    protected function createServiceRequest(
        string $title,
        string $description,
        ServiceRequestPriority $priority,
        Contact $contact,
        array $aiResolutionData,
    ): ServiceRequest {
        $status = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Open)
            ->where('name', 'New')
            ->where('is_system_protected', true)
            ->firstOrFail();

        $serviceRequest = new ServiceRequest([
            'title' => $title,
            'close_details' => $description,
            'status_id' => $status->getKey(),
            'status_updated_at' => CarbonImmutable::now(),
        ]);

        if (! empty($aiResolutionData['attempted'])) {
            $serviceRequest->is_ai_resolution_attempted = true;
            $serviceRequest->ai_resolution_confidence_score = $aiResolutionData['confidence_score'] ?? null;
            $serviceRequest->is_ai_resolution_successful = $aiResolutionData['successful'] ?? false;
        }

        $serviceRequest->respondent()->associate($contact);
        $serviceRequest->priority()->associate($priority);
        $serviceRequest->save();

        return $serviceRequest;
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function createFormSubmission(
        ServiceRequestType $type,
        ServiceRequestPriority $priority,
        array $formData,
        ServiceRequest $serviceRequest,
    ): void {
        if (empty($type->form)) {
            return;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        if (empty($formData)) {
            return;
        }

        $submission = $form->submissions()->make([
            'submitted_at' => now(),
        ]);

        $submission->priority()->associate($priority);
        $submission->save();

        $this->processFormFields($form, $formData, $submission);

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);

        if ($submission->author) {
            $serviceRequest->respondent()->associate($submission->author);
        }

        $serviceRequest->save();
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function processFormFields(
        ServiceRequestForm $form,
        array $formData,
        mixed $submission,
    ): void {
        foreach ($form->steps as $step) {
            $fields = $step->fields
                ->pluck('type', 'id')
                ->all();

            foreach ($step->fields as $field) {
                $fieldId = $field->getKey();

                if (! array_key_exists($fieldId, $formData)) {
                    continue;
                }

                $response = $formData[$fieldId];

                if ($response === null || $response === '') {
                    continue;
                }

                app(ProcessServiceRequestSubmissionField::class)->execute(
                    $submission,
                    $fieldId,
                    $response,
                    $fields,
                );
            }
        }

        $submission->save();
    }

    /**
     * @param array<int, array{question: string, answer: string}> $questionsData
     */
    protected function storeClarifyingQuestions(
        array $questionsData,
        ServiceRequest $serviceRequest,
        Contact $contact,
    ): void {
        if (empty($questionsData)) {
            return;
        }

        foreach ($questionsData as $qa) {
            $question = $qa['question'] ?? '';
            $answer = $qa['answer'] ?? '';

            if (empty($question) || empty($answer)) {
                continue;
            }

            $questionUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => (string) Str::orderedUuid(),
                'update' => $question,
                'internal' => false,
                'created_by_id' => $serviceRequest->getKey(),
                'created_by_type' => $serviceRequest->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $questionUpdate);

            $answerUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => (string) Str::orderedUuid(),
                'update' => $answer,
                'internal' => false,
                'created_by_id' => $contact->getKey(),
                'created_by_type' => $contact->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $answerUpdate);
        }
    }

    protected function assignServiceRequest(ServiceRequest $serviceRequest): void
    {
        $assignmentClass = $serviceRequest->priority->type->assignment_type?->getAssignerClass();

        if ($assignmentClass) {
            $assignmentClass->execute($serviceRequest);
        }
    }
}
