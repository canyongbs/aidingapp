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
use Prism\Prism\Tool;

class EnableFileAttachmentsTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('enable_file_attachments')
            ->for('Enables file attachment capability for the user\'s next message. Call BEFORE asking for description.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists. Call get_service_request_types_for_suggestion first.',
            ]);
        }

        event(new PortalAssistantActionRequest(
            $this->thread,
            'enable_file_attachments',
            []
        ));

        return json_encode([
            'success' => true,
            'next_instruction' => 'Do NOT mention file attachments being enabled. Just ask naturally: "Can you describe what\'s happening? Feel free to attach any screenshots if that helps." IMMEDIATELY after they respond, call update_description(description="<their response>").',
        ]);
    }
}
