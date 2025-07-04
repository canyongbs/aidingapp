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

use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AidingApp\Form\Filament\Blocks\SelectFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AidingApp\Portal\Jobs\PersistServiceRequestUpload;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CreateServiceRequestController extends Controller
{
    public function create(
        GenerateFormKitSchema $generateSchema,
        ResolveUploadsMediaCollectionForServiceRequest $resolveUploadsMediaCollectionForServiceRequest,
        ServiceRequestType $type
    ): JsonResponse {
        return response()->json([
            'schema' => $generateSchema($this->generateForm($type, $resolveUploadsMediaCollectionForServiceRequest())),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        ResolveUploadsMediaCollectionForServiceRequest $resolveUploadsMediaCollectionForServiceRequest,
        ServiceRequestType $type,
    ): JsonResponse {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        $uploadsMediaCollection = $resolveUploadsMediaCollectionForServiceRequest();

        $form = $this->generateForm($type, $uploadsMediaCollection);

        $validator = Validator::make($request->all(), [
            ...$generateValidation($form),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => (object) $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = collect($validator->validated());

        $priority = $type->priorities()->findOrFail($data->pull('Main.priority'));

        DB::beginTransaction();

        try {
            $serviceRequestStatus = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Open)
                ->where('name', 'New')
                ->where('is_system_protected', true)
                ->firstOrFail();
            $serviceRequest = new ServiceRequest([
                'title' => $data->pull('Main.title'),
                'close_details' => $data->pull('Main.description'),
                'status_id' => $serviceRequestStatus->getKey(),
                'status_updated_at' => now(),
            ]);

            $serviceRequest->respondent()->associate($contact);
            $serviceRequest->priority()->associate($priority);

            $saving = $serviceRequest->save();

            if (! $saving) {
                report(new Exception('Failed to save Service Request: ' . json_encode($serviceRequest->attributesToArray())));

                DB::rollBack();

                return response()->json([
                    'errors' => ['An error occurred while saving the Service Request.'],
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $serviceRequest->refresh();

            $assignmentClass = $serviceRequest->priority->type?->assignment_type?->getAssignerClass();

            if ($assignmentClass) {
                $assignmentClass->execute($serviceRequest);
            }

            $files = collect($data->pull('Main.upload-file', []));

            Bus::batch([
                ...$files->map(function ($file) use ($uploadsMediaCollection, $serviceRequest) {
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

            $submission = $form->submissions()
                ->make([
                    'submitted_at' => now(),
                ]);

            $submission->priority()->associate($priority);

            $data->pull('recaptcha-token');

            if ($data->filter()->isEmpty()) {
                DB::commit();

                return response()->json([
                    'message' => 'Service Request Form submitted successfully.',
                ]);
            }

            $submission->save();

            foreach ($form->steps as $step) {
                $fields = $step->fields
                    ->pluck('type', 'id')
                    ->all();

                foreach ($data[$step->label] ?? [] as $fieldId => $response) {
                    $this->processSubmissionField(
                        $submission,
                        $fieldId,
                        $response,
                        $fields,
                        $resolveSubmissionAuthorFromEmail
                    );
                }
            }

            $submission->save();

            $serviceRequest->serviceRequestFormSubmission()->associate($submission);

            if ($submission->author) {
                $serviceRequest->respondent()->associate($submission->author);
            }

            $serviceRequest->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'errors' => ['An error occurred while submitting the Service Request Form.'],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Service Request Form submitted successfully.',
        ]);
    }

    private function processSubmissionField(
        ServiceRequestFormSubmission $submission,
        string $fieldId,
        mixed $response,
        array $fields,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail
    ): void {
        $submission->fields()->attach($fieldId, [
            'id' => Str::orderedUuid(),
            'response' => $response,
        ]);

        if ($submission->author) {
            return;
        }

        if ($fields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
            return;
        }

        $author = $resolveSubmissionAuthorFromEmail($response);

        if (! $author) {
            return;
        }

        $submission->author()->associate($author);
    }

    private function generateForm(ServiceRequestType $type, UploadsMediaCollection $uploadsMediaCollection): ServiceRequestForm
    {
        $form = $type->form ?? new ServiceRequestForm();

        $content = collect(data_get($form, 'content.content', []));

        if ($content->isNotEmpty() && $form->steps->isEmpty()) {
            $form->steps->push($this->formatStep('Details', 0, $content));
        }

        $form->is_wizard = true;

        $content = collect([
            $this->formatBlock('Title', TextInputFormFieldBlock::type()),
            $this->formatBlock('Description', TextAreaFormFieldBlock::type()),
            $this->formatBlock('Priority', SelectFormFieldBlock::type(), data: [
                'options' => $type
                    ->priorities()
                    ->orderByDesc('order')
                    ->pluck('name', 'id'),
                'placeholder' => 'Select a priority',
            ]),
            $this->formatBlock('Upload File', UploadFormFieldBlock::type(), false, [
                'multiple' => $uploadsMediaCollection->getMaxNumberOfFiles() > 1,
                'limit' => $uploadsMediaCollection->getMaxNumberOfFiles(),
                'accept' => $uploadsMediaCollection->getExtensionsFull(),
                'size' => $uploadsMediaCollection->getMaxFileSizeInMB(),
                'uploadUrl' => route('api.portal.service-request.request-upload-url'),
            ]),
        ]);

        $form->steps->prepend($this->formatStep('Main', -1, $content));

        return $form;
    }

    private function formatStep(string $label, int $order, Collection $content): ServiceRequestFormStep
    {
        $step = new ServiceRequestFormStep([
            'label' => $label,
            'order' => $order,
            'content' => [
                'content' => $content,
                'type' => 'doc',
            ],
        ]);

        $content->each(fn (array $block) => $this->addFieldToStep($step, $block));

        return $step;
    }

    private function addFieldToStep(ServiceRequestFormStep $step, array $block): void
    {
        $attributes = collect($block['attrs']);

        $step->fields->push(
            (new ServiceRequestFormField())
                ->forceFill([
                    'id' => $attributes->pull('id'),
                    'type' => $attributes->pull('type'),
                    'label' => $attributes->pull('data.label'),
                    'is_required' => $attributes->pull('data.isRequired'),
                    'config' => $attributes->pull('data'),
                ])
        );
    }

    private function formatBlock(string $label, string $type, bool $required = true, array $data = []): array
    {
        return [
            'type' => 'tiptapBlock',
            'attrs' => [
                'id' => str($label)->slug()->toString(),
                'type' => $type,
                'data' => [
                    'label' => $label,
                    'isRequired' => $required,
                    ...$data,
                ],
            ],
        ];
    }
}
