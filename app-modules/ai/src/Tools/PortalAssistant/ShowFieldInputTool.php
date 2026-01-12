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

use AidingApp\Ai\Events\PortalAssistant\PortalAssistantActionRequest;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\LogsToolExecution;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use Prism\Prism\Tool;

class ShowFieldInputTool extends Tool
{
    use FindsDraftServiceRequest;
    use LogsToolExecution;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('show_field_input')
            ->for('Displays a UI widget for complex form fields like selects, radio buttons, dates, phone numbers, addresses, or file uploads. Do NOT use this for simple text fields. The system will automatically prompt the user.')
            ->withStringParameter('field_id', 'The UUID of the form field to display')
            ->using($this);
    }

    public function __invoke(string $field_id): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            $result = json_encode([
                'error' => true,
                'message' => 'No draft exists. Call fetch_service_request_types first.',
            ]);
            $this->logToolResult('show_field_input', $result, ['field_id' => $field_id]);
            return $result;
        }

        $draft->load('priority.type');

        $type = $draft->priority?->type;

        if (! $type) {
            $result = json_encode([
                'error' => true,
                'message' => 'No type selected. User must select a type first.',
            ]);
            $this->logToolResult('show_field_input', $result, ['field_id' => $field_id]);
            return $result;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        $field = null;

        foreach ($form->steps as $step) {
            foreach ($step->fields as $f) {
                if ($f->getKey() === $field_id) {
                    $field = $f;

                    break 2;
                }
            }
        }

        if (! $field) {
            $result = json_encode([
                'error' => true,
                'message' => 'Field not found for this service request type.',
            ]);
            $this->logToolResult('show_field_input', $result, ['field_id' => $field_id]);
            return $result;
        }

        event(new PortalAssistantActionRequest(
            $this->thread,
            'render_field_input',
            [
                'field_id' => $field->getKey(),
                'field_config' => [
                    'label' => $field->label,
                    'type' => $field->type,
                    'required' => (bool) $field->is_required,
                    'config' => $field->config,
                ],
            ]
        ));

        $result = 'Widget displayed to user.';
        $this->logToolResult('show_field_input', $result, [
            'field_id' => $field_id,
            'field_label' => $field->label,
            'field_type' => $field->type,
        ]);
        
        return $result;
    }
}
