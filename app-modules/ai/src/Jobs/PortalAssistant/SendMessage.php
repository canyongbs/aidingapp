<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Ai\Jobs\PortalAssistant;

use AidingApp\Ai\Enums\AiReasoningEffort;
use AidingApp\Ai\Events\PortalAssistant\PortalAssistantMessageChunk;
use AidingApp\Ai\Models\PortalAssistantMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Ai\Support\StreamingChunks\Finish;
use AidingApp\Ai\Support\StreamingChunks\Meta;
use AidingApp\Ai\Support\StreamingChunks\Text;
use AidingApp\Ai\Support\StreamingChunks\ToolCall;
use AidingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\ToolResult;
use Throwable;

class SendMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    /**
     * @param array<string, mixed> $request
     */
    public function __construct(
        protected PortalAssistantThread $thread,
        protected string $content,
        protected array $request = [],
        protected ?string $internalContent = null,
    ) {}

    public function handle(): void
    {
        $message = new PortalAssistantMessage();
        $message->thread()->associate($this->thread);
        $message->author()->associate($this->thread->author);
        $message->content = $this->content;
        $message->internal_content = $this->internalContent;
        $message->request = $this->request;
        $message->is_assistant = false;
        $message->save();

        $supportAssistantSettings = app(AiSupportAssistantSettings::class);
        $context = filled($supportAssistantSettings->instructions)
            ? $supportAssistantSettings->instructions
            : AiSupportAssistantSettings::defaultInstructions();

        try {
            $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

            $nextRequestOptions = $this->thread->messages()->where('is_assistant', true)->latest()->value('next_request_options') ?? [];

            $messages = [
                new UserMessage($this->content),
                ...(filled($this->internalContent) ? [new DeveloperMessage($this->internalContent)] : []),
            ];

            retry(3, function () use ($aiService, $context, $nextRequestOptions, $messages) {
                $stream = $aiService->streamRaw(
                    prompt: $context,
                    files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
                    options: $nextRequestOptions,
                    messages: $messages,
                    reasoningEffort: AiReasoningEffort::Minimal,
                );

                $response = new PortalAssistantMessage();
                $response->thread()->associate($this->thread);
                $response->content = '';
                $response->context = $context;
                $response->is_assistant = true;

                $chunkBuffer = [];
                $chunkCount = 0;

                foreach ($stream() as $chunk) {
                    if ($chunk instanceof Meta) {
                        $response->message_id = $chunk->messageId;
                        $response->next_request_options = $chunk->nextRequestOptions;

                        continue;
                    }

                    if ($chunk instanceof ToolCall) {
                        continue;
                    }

                    if ($chunk instanceof ToolResult) {
                        continue;
                    }

                    if ($chunk instanceof Finish) {
                        if ($chunk->error) {
                            Log::error('Portal Assistant: Stream finished with error', [
                                'thread_id' => $this->thread->getKey(),
                                'error' => $chunk->error,
                                'finishReason' => $chunk->finishReason?->name,
                            ]);
                        }

                        continue;
                    }

                    if ($chunk instanceof Text) {
                        $chunkBuffer[] = $chunk->content;
                        $chunkCount++;

                        if ($chunkCount >= 10) {
                            event(new PortalAssistantMessageChunk(
                                $this->thread,
                                content: implode('', $chunkBuffer),
                            ));
                            $response->content .= implode('', $chunkBuffer);

                            $chunkBuffer = [];
                            $chunkCount = 0;
                        }
                    }
                }

                if (! empty($chunkBuffer)) {
                    event(new PortalAssistantMessageChunk(
                        $this->thread,
                        content: implode('', $chunkBuffer),
                    ));
                    $response->content .= implode('', $chunkBuffer);
                }

                event(new PortalAssistantMessageChunk(
                    $this->thread,
                    content: '',
                    isComplete: true,
                ));

                $response->save();
                $this->thread->touch();
            }, sleepMilliseconds: 1000, when: function (Throwable $exception) {
                return $exception instanceof PrismException;
            });
        } catch (Throwable $exception) {
            report($exception);

            event(new PortalAssistantMessageChunk(
                $this->thread,
                content: '',
                isComplete: false,
                error: 'An error happened when sending your message.',
            ));
        }
    }
}
