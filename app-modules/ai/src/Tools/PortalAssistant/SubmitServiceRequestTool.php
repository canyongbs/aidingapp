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
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Prism\Prism\Tool;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;

class SubmitServiceRequestTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('submit_service_request')
            ->for('Validates and submits the service request for enrichment. Call this after all required fields are filled.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'success' => false,
                'errors' => ['draft' => 'No draft exists. Call fetch_service_request_types first.'],
            ]);
        }

        $draft->load(['priority.type', 'serviceRequestFormSubmission']);

        $type = $draft->priority?->type;

        if (! $type) {
            return json_encode([
                'success' => false,
                'errors' => ['type' => 'No type selected. User must select a type first.'],
            ]);
        }

        $errors = $this->validate($draft, $type);

        if (! empty($errors)) {
            return json_encode([
                'success' => false,
                'errors' => $errors,
                'missing_required' => array_keys($errors),
            ]);
        }

        $draft->workflow_phase = 'clarifying_questions';
        $draft->save();

        return json_encode([
            'success' => true,
            'workflow_phase' => 'clarifying_questions',
            'clarifying_questions_required' => 3,
            'instruction' => 'Ask the user 3 clarifying questions specific to their request. Questions should help understand the issue better.',
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function validate(ServiceRequest $draft, mixed $type): array
    {
        $errors = [];

        if (empty($draft->title)) {
            $errors['title'] = 'Title is required.';
        }

        if (empty($draft->close_details)) {
            $errors['description'] = 'Description is required.';
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        $submission = $draft->serviceRequestFormSubmission;
        $filledFieldIds = [];

        if ($submission) {
            $filledFieldIds = $submission->fields()
                ->get()
                ->filter(fn ($field) => $field->pivot->response !== null && $field->pivot->response !== '')
                ->pluck('id')
                ->all();
        }

        foreach ($form->steps as $step) {
            if ($step->label === 'Main' || $step->label === 'Questions') {
                continue;
            }

            foreach ($step->fields as $field) {
                if ($field->is_required && ! in_array($field->getKey(), $filledFieldIds)) {
                    $errors[$field->getKey()] = "{$field->label} is required.";
                }
            }
        }

        return $errors;
    }
}
