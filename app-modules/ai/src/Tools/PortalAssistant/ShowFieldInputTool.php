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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Events\PortalAssistant\PortalAssistantActionRequest;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use Prism\Prism\Tool;

class ShowFieldInputTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('show_field_input')
            ->for('Displays a UI widget for fields with collection_method="show_field_input". In the SAME response, ask a natural question prompting the user to complete it. Do NOT use for text fields.')
            ->withStringParameter('field_id', 'The UUID of the form field to display')
            ->using($this);
    }

    /** @phpstan-ignore MeliorStan.parameterNameNotCamelCase (tool parameter names must match AI tool definition) */
    public function __invoke(string $field_id): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'error' => true,
                'message' => 'No draft exists. Call get_service_request_types_for_suggestion first.',
            ]);
        }

        $draft->load('priority.type');

        $type = $draft->priority?->type;

        if (! $type) {
            return json_encode([
                'error' => true,
                'message' => 'No type selected. User must select a type first.',
            ]);
        }

        $form = $type->form;

        if (! $form) {
            return json_encode([
                'error' => true,
                'message' => 'No form found for this service request type.',
            ]);
        }

        $field = $form->fields->firstWhere('id', $field_id);

        if (! $field) {
            return json_encode([
                'error' => true,
                'message' => 'Field not found for this service request type.',
            ]);
        }

        // Check if this is a text field that shouldn't use the widget
        $isTextField = in_array($field->type, [
            TextInputFormFieldBlock::type(),
            TextAreaFormFieldBlock::type(),
        ]);

        if ($isTextField) {
            return json_encode([
                'success' => false,
                'hint' => "This is a text field (collection_method=\"text\"). Ask the user naturally (e.g., \"What's your {$field->label}?\") and then call update_form_field(field_id=\"{$field_id}\", value=\"<their response>\") with their answer.",
            ]);
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

        return json_encode([
            'success' => true,
            'field_label' => $field->label,
            'next_instruction' => "Do NOT mention the widget or that something is displayed. Just ask ONE natural question (e.g., \"What's your {$field->label}?\" or \"Could you select the {$field->label}?\"). Do NOT list the options or add explanation - the user sees the input. Wait for their selection.",
        ]);
    }
}
