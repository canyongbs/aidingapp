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
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Prism\Prism\Tool;

class RequestFieldInputTool extends Tool
{
    protected const CONVERSATIONAL_TYPES = [
        'text_input',
        'text_area',
        'number',
        'email',
        'checkbox',
    ];

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('request_field_input')
            ->for('Displays a UI widget for collecting complex field input like selects, dates, file uploads, etc. For simple text fields, collect the value conversationally instead.')
            ->withStringParameter('field_id', 'The UUID of the form field to display')
            ->using($this);
    }

    public function __invoke(string $field_id): string
    {
        $field = ServiceRequestFormField::find($field_id);

        if (! $field) {
            return json_encode([
                'error' => true,
                'message' => 'Field not found.',
            ]);
        }

        if (in_array($field->type, self::CONVERSATIONAL_TYPES, true)) {
            return json_encode([
                'needs_ui' => false,
                'message' => "This field type ({$field->type}) can be collected conversationally. Ask the user for: {$field->label}",
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

        return "Tell the user: I've shown you an input for \"{$field->label}\". Please provide the requested information.";
    }
}
