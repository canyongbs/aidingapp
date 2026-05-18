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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Contact\Models\Contact;
use AidingApp\Form\Actions\ResolveBlockRegistry;
use AidingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\Portal\Jobs\PersistServiceRequestUpload;
use AidingApp\ServiceManagement\Actions\AssignServiceRequestToTeam;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StoreServiceRequestController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user() ?? $request->user();

        abort_if(! ($contact instanceof Contact), Response::HTTP_UNAUTHORIZED);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'uuid'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.path' => [
                'required_with:attachments',
                'string',
                'regex:/^tmp\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.[a-zA-Z0-9]+$/i',
            ],
            'attachments.*.original_file_name' => ['required_with:attachments', 'string', 'max:255'],
            'custom_fields' => ['nullable', 'array'],
            'questions' => ['nullable', 'array'],
            'questions.*' => ['required', 'string'],
            'is_ai_resolution_attempted' => ['nullable', 'boolean'],
            'is_ai_resolution_successful' => ['nullable', 'boolean'],
            'ai_resolution_confidence_score' => ['nullable', 'integer', 'min:1', 'max:100'],
            'encrypted_ai_proposed_answer' => ['required_if:is_ai_resolution_attempted,true', 'nullable', 'string'],
        ]);

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

        $form = null;
        $customFieldData = collect();

        if (! empty($data['custom_fields']) && $type->form) {
            $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

            $validation = $this->buildCustomFieldValidation($form);

            try {
                $validated = Validator::make(
                    $data['custom_fields'],
                    $validation['rules'],
                    [],
                    $validation['attributes'],
                )->validate();
                $customFieldData = collect($validated);
            } catch (ValidationException $exception) {
                return response()->json([
                    'errors' => (object) $exception->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::beginTransaction();

        try {
            $priority = $type->priorities()->findOrFail($data['priority_id']);

            $serviceRequestStatus = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Open)
                ->where('name', 'New')
                ->where('is_system_protected', true)
                ->firstOrFail();

            $serviceRequest = new ServiceRequest([
                'title' => $data['title'],
                'close_details' => $data['description'],
                'status_id' => $serviceRequestStatus->getKey(),
                'status_updated_at' => CarbonImmutable::now(),
            ]);

            $serviceRequest->respondent()->associate($contact);
            $serviceRequest->priority()->associate($priority);
            $serviceRequest->save();
            $serviceRequest->refresh();

            $updateUuids = $this->generateUpdateUuids($data);

            $this->storeClarifyingQuestions($data, $serviceRequest, $contact, $type, $updateUuids);

            $this->handleAiResolution($data, $serviceRequest, $contact, $type, $updateUuids);

            app(AssignServiceRequestToTeam::class)->execute($serviceRequest);

            Bus::batch(
                array_map(
                    fn (array $file) => new PersistServiceRequestUpload(
                        $serviceRequest,
                        $file['path'],
                        $file['original_file_name'],
                        $uploadsMediaCollection->getName(),
                    ),
                    $data['attachments'] ?? [],
                ),
            )
                ->name("persist-service-request-uploads-{$serviceRequest->getKey()}")
                ->dispatchAfterResponse();

            if ($form && $customFieldData->isNotEmpty()) {
                $this->createFormSubmission($form, $customFieldData, $serviceRequest, $priority);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return response()->json(
                ['errors' => ['An error occurred while submitting the service request.']],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json([
            'message' => 'Service request submitted successfully.',
            'service_request_id' => $serviceRequest->getKey(),
        ]);
    }

    /**
     * @return array{rules: array<string, array<int, string>>, attributes: array<string, string>}
     */
    protected function buildCustomFieldValidation(ServiceRequestForm $form): array
    {
        $blocks = app(ResolveBlockRegistry::class)($form, true);
        $rules = [];
        $attributes = [];

        foreach ($form->steps as $step) {
            if ($step->label === 'Main') {
                continue;
            }

            foreach ($step->fields as $field) {
                $fieldRules = collect();

                if ($field->is_required) {
                    $fieldRules->push('required');
                } else {
                    $fieldRules->push('nullable');
                }

                if ($field->type && isset($blocks[$field->type])) {
                    $fieldRules = $fieldRules->merge($blocks[$field->type]::getValidationRules($field));
                }

                $rules[$field->getKey()] = $fieldRules->all();
                $attributes[$field->getKey()] = $field->label;
            }
        }

        return ['rules' => $rules, 'attributes' => $attributes];
    }

    /**
     * @param  Collection<string, mixed>  $customFieldData
     */
    protected function createFormSubmission(
        ServiceRequestForm $form,
        Collection $customFieldData,
        ServiceRequest $serviceRequest,
        ServiceRequestPriority $priority,
    ): void {
        $submission = $form->submissions()->make([
            'submitted_at' => now(),
        ]);

        $submission->priority()->associate($priority);
        $submission->save();

        $uploadsCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

        foreach ($form->steps as $step) {
            if ($step->label === 'Main') {
                continue;
            }

            $fields = $step->fields->pluck('type', 'id')->all();

            foreach ($step->fields as $field) {
                $response = $customFieldData[$field->getKey()] ?? null;

                if ($response === null) {
                    continue;
                }

                if ($field->type === UploadFormFieldBlock::type() && is_array($response)) {
                    $response = $this->persistUploadFieldFiles($serviceRequest, $response, $uploadsCollection?->getName() ?? 'uploads');
                }

                app(ProcessServiceRequestSubmissionField::class)->execute(
                    $submission,
                    $field->getKey(),
                    $response,
                    $fields,
                );
            }
        }

        $submission->save();

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);
        $serviceRequest->save();
    }

    /**
     * @param  array<int, array<string, string>>  $files
     *
     * @return array<int, string>
     */
    protected function persistUploadFieldFiles(ServiceRequest $serviceRequest, array $files, string $collection): array
    {
        $mediaIds = [];

        foreach ($files as $file) {
            $path = $file['path'] ?? null;
            $originalFileName = $file['originalFileName'] ?? $file['original_file_name'] ?? 'file';

            if (! $path || ! Storage::exists($path)) {
                continue;
            }

            $media = $serviceRequest
                ->addMediaFromDisk($path)
                ->usingName(pathinfo($originalFileName, PATHINFO_FILENAME))
                ->toMediaCollection($collection);

            Storage::delete($path);

            $mediaIds[] = $media->getKey();
        }

        return $mediaIds;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return Collection<int, non-empty-string>
     */
    protected function generateUpdateUuids(array $data): Collection
    {
        $count = 0;

        $questions = $data['questions'] ?? [];
        $count += count($questions) * 2;

        if (($data['is_ai_resolution_attempted'] ?? false) && ($data['encrypted_ai_proposed_answer'] ?? null)) {
            $count += 1;

            if ($data['is_ai_resolution_successful'] ?? false) {
                $count += 1;
            } else {
                $count += 2;
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
     * @param  array<string, mixed>  $data
     * @param  Collection<int, non-empty-string>  $updateUuids
     */
    protected function storeClarifyingQuestions(
        array $data,
        ServiceRequest $serviceRequest,
        Contact $contact,
        ServiceRequestType $type,
        Collection $updateUuids
    ): void {
        $questions = $data['questions'] ?? [];

        if (empty($questions)) {
            return;
        }

        foreach ($questions as $encryptedQuestion => $answer) {
            $payload = json_decode(decrypt($encryptedQuestion), true);

            abort_if(
                ! is_array($payload)
                    || ($payload['contact_id'] ?? null) !== $contact->getKey()
                    || ($payload['type_id'] ?? null) !== $type->getKey(),
                Response::HTTP_FORBIDDEN
            );

            $questionUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => $payload['text'],
                'update_type' => ServiceRequestUpdateType::ClarifyingQuestion,
                'internal' => false,
                'created_by_id' => $serviceRequest->getKey(),
                'created_by_type' => $serviceRequest->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $questionUpdate);

            $answerUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => $answer,
                'update_type' => ServiceRequestUpdateType::ClarifyingAnswer,
                'internal' => false,
                'created_by_id' => $contact->getKey(),
                'created_by_type' => $contact->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $answerUpdate);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  Collection<int, non-empty-string>  $updateUuids
     */
    protected function handleAiResolution(
        array $data,
        ServiceRequest $serviceRequest,
        Contact $contact,
        ServiceRequestType $type,
        Collection $updateUuids
    ): void {
        if (! ($data['is_ai_resolution_attempted'] ?? false)) {
            return;
        }

        $serviceRequest->is_ai_resolution_attempted = true;
        $serviceRequest->ai_resolution_confidence_score = $data['ai_resolution_confidence_score'] ?? null;
        $serviceRequest->is_ai_resolution_successful = false;

        $encryptedAnswer = $data['encrypted_ai_proposed_answer'] ?? null;

        if (! $encryptedAnswer) {
            $serviceRequest->save();

            return;
        }

        $payload = json_decode(decrypt($encryptedAnswer), true);

        abort_if(
            ! is_array($payload)
                || ($payload['contact_id'] ?? null) !== $contact->getKey()
                || ($payload['type_id'] ?? null) !== $type->getKey(),
            Response::HTTP_FORBIDDEN
        );

        $aiProposedAnswer = $payload['text'];
        $confidenceScore = $data['ai_resolution_confidence_score'] ?? 0;

        $aiAnswerUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => "Based on the information you've provided, here is a potential solution:\n\n{$aiProposedAnswer}\n\nDid this resolve your issue?",
            'update_type' => ServiceRequestUpdateType::AiResolutionProposed,
            'internal' => false,
            'created_by_id' => $serviceRequest->getKey(),
            'created_by_type' => $serviceRequest->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($serviceRequest, $aiAnswerUpdate);

        if ($data['is_ai_resolution_successful'] ?? false) {
            $serviceRequest->is_ai_resolution_successful = true;

            $userConfirmationUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => 'Yes, this resolved my issue.',
                'update_type' => ServiceRequestUpdateType::AiResolutionResponse,
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
        } else {
            $serviceRequest->is_ai_resolution_successful = false;

            $userDeclineUpdate = $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => 'No, this did not resolve my issue.',
                'update_type' => ServiceRequestUpdateType::AiResolutionResponse,
                'internal' => false,
                'created_by_id' => $contact->getKey(),
                'created_by_type' => $contact->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($serviceRequest, $userDeclineUpdate);

            $serviceRequest->serviceRequestUpdates()->createQuietly([
                'id' => $updateUuids->shift(),
                'update' => "AI Resolution Attempt (Confidence: {$confidenceScore}%)\n\nProposed Answer:\n{$aiProposedAnswer}\n\nUser indicated this did not resolve their issue.",
                'update_type' => ServiceRequestUpdateType::AiResolutionSummary,
                'internal' => true,
                'created_by_id' => $serviceRequest->getKey(),
                'created_by_type' => $serviceRequest->getMorphClass(),
            ]);
        }

        $serviceRequest->save();
    }
}
