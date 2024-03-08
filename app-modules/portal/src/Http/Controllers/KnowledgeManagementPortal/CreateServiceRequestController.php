<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequest;
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
        $contact = auth('contact')->user();
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

        ray('database', config('database.connections.tenant'));

        ServiceRequest::create([
            'service_request_form_submission_id' => $submission->id,
            'title' => $submission->submissible->name,
            'respondent_type' => $submission->author->getMorphClass(),
            'respondent_id' => $submission->author->getKey(),
            'priority_id' => $submission->service_request_priority_id,
        ]);

        return response()->json(
            [
                'message' => 'Service Request Form submitted successfully.',
            ]
        );
    }
}
