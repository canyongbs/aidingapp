<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Form\Filament\Blocks\SelectFormFieldBlock;
use AidingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;

class CreateServiceRequestController extends Controller
{
    public function create(GenerateFormKitSchema $generateSchema, ServiceRequestType $type): JsonResponse
    {
        return response()->json([
            'schema' => $generateSchema($this->generateForm($type)),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        ServiceRequestType $type,
    ): JsonResponse {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        $form = $this->generateForm($type);

        ray($request, $request->all());

        dd('stop');

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

        return DB::transaction(function () use (
            $resolveSubmissionAuthorFromEmail,
            $form,
            $priority,
            $contact,
            $data,
        ) {
            $serviceRequest = new ServiceRequest([
                'title' => $data->pull('Main.title'),
                'close_details' => $data->pull('Main.description'),
            ]);

            $serviceRequest->respondent()->associate($contact);
            $serviceRequest->priority()->associate($priority);

            $serviceRequest->save();

            $submission = $form->submissions()
                ->make([
                    'submitted_at' => now(),
                ]);

            $submission->priority()->associate($priority);

            $data->pull('recaptcha-token');

            if ($data->filter()->isEmpty()) {
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

            return response()->json([
                'message' => 'Service Request Form submitted successfully.',
            ]);
        });
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

    private function generateForm(ServiceRequestType $type): ServiceRequestForm
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
                'multiple' => true,
                'limit' => 5,
                'accept' => '.pdf,.doc,.docx,.xml,.md,.csv,.png',
                'size' => 5,
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
