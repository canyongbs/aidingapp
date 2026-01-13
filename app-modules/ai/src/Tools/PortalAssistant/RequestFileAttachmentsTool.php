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
use Prism\Prism\Tool;

class RequestFileAttachmentsTool extends Tool
{
    use FindsDraftServiceRequest;
    use LogsToolExecution;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('request_file_attachments')
            ->for('Enables optional file attachment capability for the user. Call this BEFORE asking for the description to allow users to attach supporting files (screenshots, documents, etc.) with their message. The attachment UI will be enabled until the user sends their next message.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            $result = json_encode([
                'success' => false,
                'error' => 'No draft exists. Call fetch_service_request_types first.',
            ]);
            $this->logToolResult('request_file_attachments', $result, []);

            return $result;
        }

        event(new PortalAssistantActionRequest(
            $this->thread,
            'enable_file_attachments',
            []
        ));

        $result = json_encode([
            'success' => true,
            'instruction' => 'File attachment button is now visible to the user. In your response, mention that they can attach relevant files if they have any. The attachments will be included with their next message.',
        ]);

        $this->logToolResult('request_file_attachments', $result, []);

        return $result;
    }
}
