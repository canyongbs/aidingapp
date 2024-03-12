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
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;

class CreateServiceRequestController extends Controller
{
    public function create(GenerateFormKitSchema $generateSchema, ServiceRequestType $type): JsonResponse
    {
        return response()->json([
            'schema' => $generateSchema($type->form),
            'priorities' => $type->priorities()->orderBy('order', 'desc')->get()->pluck('name', 'id')->map(fn ($name, $id) => ['id' => $id, 'name' => $name]),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        ServiceRequestType $type,
    ): JsonResponse {
        $contact = auth('contact')->user() ?? $request->user();

        $serviceRequestForm = $type->form;

        if (
            is_null($contact)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            $generateValidation($serviceRequestForm)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $submission = $serviceRequestForm->submissions()->make();

        $submission
            ->priority()
            ->associate(
                $serviceRequestForm
                    ->type
                    ->priorities()
                    ->findOrFail(
                        $request->input('priority')
                    )
            );

        if ($contact) {
            $submission->author()->associate($contact);
        }

        $submission->submitted_at = now();

        $submission->description = $request->input('description');

        $submission->save();

        $data = $validator->validated();

        unset($data['recaptcha-token']);

        if ($serviceRequestForm->is_wizard) {
            foreach ($serviceRequestForm->steps as $step) {
                $stepFields = $step->fields()->pluck('type', 'id')->all();

                foreach ($data[$step->label] as $fieldId => $response) {
                    $submission->fields()->attach(
                        $fieldId,
                        ['id' => Str::orderedUuid(), 'response' => $response],
                    );

                    if ($submission->author) {
                        continue;
                    }

                    if ($stepFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                        continue;
                    }

                    $author = $resolveSubmissionAuthorFromEmail($response);

                    if (! $author) {
                        continue;
                    }

                    $submission->author()->associate($author);
                }
            }
        } else {
            $formFields = $serviceRequestForm->fields()->pluck('type', 'id')->all();

            foreach ($data as $fieldId => $response) {
                $submission->fields()->attach(
                    $fieldId,
                    ['id' => Str::orderedUuid(), 'response' => $response],
                );

                if ($submission->author) {
                    continue;
                }

                if ($formFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                    continue;
                }

                $author = $resolveSubmissionAuthorFromEmail($response);

                if (! $author) {
                    continue;
                }

                $submission->author()->associate($author);
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Service Request Form submitted successfully.',
            ]
        );
    }
}
