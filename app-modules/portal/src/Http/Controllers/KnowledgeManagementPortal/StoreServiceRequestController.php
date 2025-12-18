<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Contact\Models\Contact;
use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\Portal\Jobs\PersistServiceRequestUpload;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StoreServiceRequestController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        try {
            $data = $this->validateRequest($request, $form);
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => (object) $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $priority = $type->priorities()->findOrFail($data->pull('Main.priority'));

        assert($priority instanceof ServiceRequestPriority);

        DB::beginTransaction();

        try {
            $serviceRequest = $this->createServiceRequest($data, $priority, $contact);

            if (! $serviceRequest) {
                DB::rollBack();

                return response()->json([
                    'errors' => ['An error occurred while saving the Service Request.'],
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $updateUuids = $this->generateUpdateUuids($request);

            $this->storeClarifyingQuestions($request, $serviceRequest, $contact, $updateUuids);

            $this->handleAiResolution($request, $serviceRequest, $contact, $updateUuids);

            $this->assignServiceRequest($serviceRequest);

            $this->dispatchFileUploads($data, $serviceRequest, $uploadsMediaCollection);

            $hasAdditionalData = $this->createFormSubmission($form, $data, $serviceRequest, $priority);

            if (! $hasAdditionalData) {
                DB::commit();

                return response()->json([
                    'message' => 'Service Request Form submitted successfully.',
                ]);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return response()->json([
                'errors' => ['An error occurred while submitting the Service Request Form.'],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Service Request Form submitted successfully.',
        ]);
    }

    /**
     * @return Collection<string, mixed>
     */
    protected function validateRequest(Request $request, ServiceRequestForm $form): Collection
    {
        $validator = Validator::make($request->all(), [
            ...app(GenerateSubmissibleValidation::class)($form),
            'Questions' => ['nullable', 'array'],
            'Questions.*' => ['required', 'string'],
            'is_ai_resolution_attempted' => ['nullable', 'boolean'],
            'is_ai_resolution_successful' => ['nullable', 'boolean'],
            'ai_resolution_confidence_score' => ['nullable', 'integer', 'min:1', 'max:100'],
            'encrypted_ai_proposed_answer' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return collect($validator->validated())->except('Questions');
    }

    /**
     * @param Collection<string, mixed> $data
     */
    protected function createServiceRequest(
        Collection $data,
        ServiceRequestPriority $priority,
        Contact $contact
    ): ?ServiceRequest {
        $serviceRequestStatus = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Open)
            ->where('name', 'New')
            ->where('is_system_protected', true)
            ->firstOrFail();

        $serviceRequest = new ServiceRequest([
            'title' => $data->pull('Main.title'),
            'close_details' => $data->pull('Main.description'),
            'status_id' => $serviceRequestStatus->getKey(),
            'status_updated_at' => CarbonImmutable::now(),
        ]);

        $serviceRequest->respondent()->associate($contact);
        $serviceRequest->priority()->associate($priority);

        $saved = $serviceRequest->save();

        if (! $saved) {
            report(new Exception('Failed to save Service Request: ' . json_encode($serviceRequest->attributesToArray())));

            return null;
        }

        $serviceRequest->refresh();

        return $serviceRequest;
    }

    /**
     * @return Collection<int, non-empty-string>
     */
    protected function generateUpdateUuids(Request $request): Collection
    {
        $count = 0;

        $questions = $request->input('Questions', []);
        $count += count($questions) * 2;

        if ($request->boolean('is_ai_resolution_attempted') && $request->input('encrypted_ai_proposed_answer')) {
            $count += 1; // AI proposed answer

            if ($request->boolean('is_ai_resolution_successful')) {
                $count += 1; // User confirmation
            } else {
                $count += 2; // User decline + internal note
            }
        }

        if ($count === 0) {
            return collect();
        }

        return collect(range(1, $count))
            ->map(fn (): string => (string) Str::orderedUuid())
            ->sort()
            ->values();
    }

    /**
     * @param Collection<int, non-empty-string> $updateUuids
     */
    protected function storeClarifyingQuestions(
        Request $request,
        ServiceRequest $serviceRequest,
        Contact $contact,
        Collection $updateUuids
    ): void {
        $questions = $request->input('Questions', []);

        if (empty($questions)) {
            return;
        }

        foreach ($questions as $encryptedQuestion => $answer) {
            $questionUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => decrypt($encryptedQuestion),
                'internal' => false,
                'created_by_id' => $serviceRequest->getKey(),
                'created_by_type' => $serviceRequest->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $questionUpdate);

            $answerUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => $answer,
                'internal' => false,
                'created_by_id' => $contact->getKey(),
                'created_by_type' => $contact->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $answerUpdate);
        }
    }

    /**
     * @param Collection<int, non-empty-string> $updateUuids
     */
    protected function handleAiResolution(
        Request $request,
        ServiceRequest $serviceRequest,
        Contact $contact,
        Collection $updateUuids
    ): void {
        if (! $request->boolean('is_ai_resolution_attempted')) {
            return;
        }

        $serviceRequest->is_ai_resolution_attempted = true;
        $serviceRequest->ai_resolution_confidence_score = $request->integer('ai_resolution_confidence_score');
        $serviceRequest->is_ai_resolution_successful = false;

        $encryptedAnswer = $request->input('encrypted_ai_proposed_answer');

        if (! $encryptedAnswer) {
            $serviceRequest->save();

            return;
        }

        $aiProposedAnswer = decrypt($encryptedAnswer);
        $confidenceScore = $request->integer('ai_resolution_confidence_score');

        $this->createAiProposedAnswerUpdate($serviceRequest, $aiProposedAnswer, $updateUuids);

        if ($request->boolean('is_ai_resolution_successful')) {
            $this->handleAiResolutionAccepted($serviceRequest, $contact, $updateUuids);
        } else {
            $this->handleAiResolutionDeclined($serviceRequest, $contact, $aiProposedAnswer, $confidenceScore, $updateUuids);
        }

        $serviceRequest->save();
    }

    /**
     * @param Collection<int, non-empty-string> $updateUuids
     */
    protected function createAiProposedAnswerUpdate(
        ServiceRequest $serviceRequest,
        string $aiProposedAnswer,
        Collection $updateUuids
    ): void {
        $aiAnswerUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => "Based on the information you've provided, here is a potential solution:\n\n{$aiProposedAnswer}\n\nDid this resolve your issue?",
            'internal' => false,
            'created_by_id' => $serviceRequest->getKey(),
            'created_by_type' => $serviceRequest->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($serviceRequest, $aiAnswerUpdate);
    }

    /**
     * @param Collection<int, non-empty-string> $updateUuids
     */
    protected function handleAiResolutionAccepted(
        ServiceRequest $serviceRequest,
        Contact $contact,
        Collection $updateUuids
    ): void {
        $serviceRequest->is_ai_resolution_successful = true;

        $userConfirmationUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => 'Yes, this resolved my issue.',
            'internal' => false,
            'created_by_id' => $contact->getKey(),
            'created_by_type' => $contact->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($serviceRequest, $userConfirmationUpdate);

        $closedStatus = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Closed)
            ->first();

        if ($closedStatus) {
            $serviceRequest->status_id = $closedStatus->getKey();
            $serviceRequest->status_updated_at = CarbonImmutable::now();
        }
    }

    /**
     * @param Collection<int, non-empty-string> $updateUuids
     */
    protected function handleAiResolutionDeclined(
        ServiceRequest $serviceRequest,
        Contact $contact,
        string $aiProposedAnswer,
        int $confidenceScore,
        Collection $updateUuids
    ): void {
        $serviceRequest->is_ai_resolution_successful = false;

        $userDeclineUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => 'No, this did not resolve my issue.',
            'internal' => false,
            'created_by_id' => $contact->getKey(),
            'created_by_type' => $contact->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($serviceRequest, $userDeclineUpdate);

        $serviceRequest->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => "AI Resolution Attempt (Confidence: {$confidenceScore}%)\n\nProposed Answer:\n{$aiProposedAnswer}\n\nUser indicated this did not resolve their issue.",
            'internal' => true,
            'created_by_id' => $serviceRequest->getKey(),
            'created_by_type' => $serviceRequest->getMorphClass(),
        ]);
    }

    protected function assignServiceRequest(ServiceRequest $serviceRequest): void
    {
        $assignmentClass = $serviceRequest->priority->type->assignment_type->getAssignerClass();

        if ($assignmentClass) {
            $assignmentClass->execute($serviceRequest);
        }
    }

    /**
     * @param Collection<string, mixed> $data
     */
    protected function dispatchFileUploads(
        Collection $data,
        ServiceRequest $serviceRequest,
        UploadsMediaCollection $uploadsMediaCollection
    ): void {
        /** @var array<int, array{path: string, originalFileName: string}> $filesData */
        $filesData = $data->pull('Main.upload-file', []);
        $files = collect($filesData);

        Bus::batch([
            ...$files->map(function (array $file) use ($uploadsMediaCollection, $serviceRequest) {
                return new PersistServiceRequestUpload(
                    $serviceRequest,
                    $file['path'],
                    $file['originalFileName'],
                    $uploadsMediaCollection->getName(),
                );
            }),
        ])
            ->name("persist-service-request-uploads-{$serviceRequest->getKey()}")
            ->dispatchAfterResponse();
    }

    /**
     * @param Collection<string, mixed> $data
     */
    protected function createFormSubmission(
        ServiceRequestForm $form,
        Collection $data,
        ServiceRequest $serviceRequest,
        ServiceRequestPriority $priority
    ): bool {
        $submission = $form->submissions()
            ->make([
                'submitted_at' => now(),
            ]);

        $submission->priority()->associate($priority);

        $data->pull('recaptcha-token');

        if ($data->filter()->isEmpty()) {
            return false;
        }

        $submission->save();

        foreach ($form->steps as $step) {
            $fields = $step->fields
                ->pluck('type', 'id')
                ->all();

            foreach ($data[$step->label] ?? [] as $fieldId => $response) {
                app(ProcessServiceRequestSubmissionField::class)->execute(
                    $submission,
                    $fieldId,
                    $response,
                    $fields,
                );
            }
        }

        $submission->save();

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);

        if ($submission->author) {
            $serviceRequest->respondent()->associate($submission->author);
        }

        $serviceRequest->save();

        return true;
    }
}
