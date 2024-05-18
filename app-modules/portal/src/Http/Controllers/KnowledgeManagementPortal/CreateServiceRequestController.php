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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;

class CreateServiceRequestController extends Controller
{
    public function create(GenerateFormKitSchema $generateSchema, ServiceRequestType $type): JsonResponse
    {
        ray($generateSchema($type->form), $generateSchema($this->generateForm($type)));

        return response()->json([
            // 'schema' => $generateSchema($this->generateForm($type)),
            'schema' => $generateSchema($type->form),
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

        $validator = Validator::make($request->all(), [
            ...$generateValidation($form),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => (object) $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = collect($validator->validated());

        $priority = $type->priorities()->findOrFail($data->pull('main.priority'));

        return DB::transaction(function () use (
            $resolveSubmissionAuthorFromEmail,
            $form,
            $priority,
            $contact,
            $data,
        ) {
            $serviceRequest = new ServiceRequest([
                'title' => $data->pull('main.title'),
                'close_details' => $data->pull('main.description'),
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

            $data = $data->filter();

            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Service Request Form submitted successfully.',
                ]);
            }

            $submission->save();

            foreach ($form->steps as $step) {
                $fields = $step->fields()->pluck('type', 'id')->all();

                foreach ($data[$step->label] as $fieldId => $response) {
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
    ) {
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
            $step = new ServiceRequestFormStep([
                'label' => 'details',
                'order' => 0,
                'content' => [
                    'content' => $form->content['content'],
                    'type' => 'doc',
                ],
            ]);

            $content->each(function ($block) use ($step) {
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
            });

            $form->steps->push($step);
        }

        $form->is_wizard = true;

        $content = collect([
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'title',
                    'type' => 'text_input',
                    'data' => [
                        'label' => 'Title',
                        'isRequired' => true,
                    ],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'description',
                    'type' => 'text_area',
                    'data' => [
                        'label' => 'Description',
                        'isRequired' => true,
                    ],
                ],
            ],
            [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'id' => 'priority',
                    'type' => 'select',
                    'data' => [
                        'label' => 'Priority',
                        'isRequired' => true,
                        'options' => $type
                            ->priorities()
                            ->orderByDesc('order')
                            ->pluck('name', 'id'),
                        'placeholder' => 'Select a priority',
                    ],
                ],
            ],
        ]);

        $step = new ServiceRequestFormStep([
            'label' => 'main',
            'order' => -1,
            'content' => [
                'content' => $content,
                'type' => 'doc',
            ],
        ]);

        $content->each(function ($block) use ($step) {
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
        });

        $form->steps->prepend($step);

        return $form;
    }
}
