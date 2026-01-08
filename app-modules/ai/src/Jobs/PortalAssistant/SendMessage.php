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

namespace AidingApp\Ai\Jobs\PortalAssistant;

use AidingApp\Ai\Events\PortalAssistant\PortalAssistantMessageChunk;
use AidingApp\Ai\Models\PortalAssistantMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Ai\Support\StreamingChunks\Meta;
use AidingApp\Ai\Support\StreamingChunks\Text;
use AidingApp\Ai\Support\StreamingChunks\ToolCall;
use AidingApp\Ai\Tools\PortalAssistant\EvaluateAiResolutionTool;
use AidingApp\Ai\Tools\PortalAssistant\GetServiceRequestFormTool;
use AidingApp\Ai\Tools\PortalAssistant\RequestFieldInputTool;
use AidingApp\Ai\Tools\PortalAssistant\SubmitServiceRequestTool;
use AidingApp\Ai\Tools\PortalAssistant\SuggestServiceRequestTypeTool;
use AidingApp\Ai\Validators\InternalContentValidator;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use App\Features\PortalAssistantServiceRequestFeature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Prism\Prism\Tool;
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
     * @param array<string, mixed>|null $internalContent
     */
    public function __construct(
        protected PortalAssistantThread $thread,
        protected string $content,
        protected array $request = [],
        protected ?array $internalContent = null,
    ) {}

    public function handle(): void
    {
        if ($this->internalContent !== null) {
            $validator = app(InternalContentValidator::class);
            $validationResult = $validator->validate($this->internalContent);

            if ($validationResult->failed()) {
                event(new PortalAssistantMessageChunk(
                    $this->thread,
                    content: '',
                    isComplete: false,
                    error: 'Invalid message data: ' . implode(' ', $validationResult->errors),
                ));

                return;
            }
        }

        $message = new PortalAssistantMessage();
        $message->thread()->associate($this->thread);
        $message->author()->associate($this->thread->author);
        $message->content = $this->content;
        $message->internal_content = $this->internalContent;
        $message->request = $this->request;
        $message->is_assistant = false;
        $message->save();

        $context = <<<EOT
            You are a helpful AI assistant for our support portal. Your role is to answer user questions by searching and referencing information from our knowledge base.
            
            Important guidelines:
            - You have access to a knowledge base containing support articles and documentation
            - Never mention "uploaded files" or suggest that the user has provided any files - the knowledge base is managed by the system, not by users
            - Only provide answers based on information found in the knowledge base
            - If you cannot find relevant information in the knowledge base, politely say "I don't have that information in our knowledge base at the moment" or suggest contacting support for further assistance
            - Do not make up or assume information that isn't in the knowledge base
            - Provide clear, concise, and accurate responses
            - Be friendly and professional in your tone
            - When referencing information, speak naturally about "our knowledge base" or "our documentation" rather than mentioning files or uploads
            
            CRITICAL: You MUST format ALL responses using Markdown. This is non-negotiable. Always use proper Markdown formatting. NEVER mention that you are responding using Markdown.
            EOT;

        if (PortalAssistantServiceRequestFeature::active()) {
            $context .= $this->buildServiceRequestInstructions();
        }

        try {
            $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

            $aiContent = $this->buildAiContent();

            $tools = $this->buildTools();

            $stream = $aiService->streamRaw(
                prompt: $context,
                content: $aiContent,
                files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
                options: $this->thread->messages()->where('is_assistant', true)->latest()->value('next_request_options') ?? [],
                tools: $tools,
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

                if ($chunk instanceof Text) {
                    $chunkBuffer[] = $chunk->content;
                    $chunkCount++;

                    if ($chunkCount >= 30) {
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

    protected function buildAiContent(): string
    {
        if ($this->internalContent === null) {
            return $this->content;
        }

        return match ($this->internalContent['type'] ?? null) {
            InternalContentValidator::TYPE_FIELD_RESPONSE => sprintf(
                '[System: User submitted form field data] %s',
                json_encode($this->internalContent, JSON_THROW_ON_ERROR)
            ),
            InternalContentValidator::TYPE_TYPE_SELECTION => sprintf(
                '[System: User selected service request type_id: %s]',
                $this->internalContent['type_id'] ?? 'unknown'
            ),
            InternalContentValidator::TYPE_WIDGET_CANCELLED => sprintf(
                '[System: User cancelled widget for field_id: %s]',
                $this->internalContent['field_id'] ?? 'unknown'
            ),
            InternalContentValidator::TYPE_WIDGET_ERROR => sprintf(
                '[System: Widget error for field_id: %s - %s]',
                $this->internalContent['field_id'] ?? 'unknown',
                $this->internalContent['error'] ?? 'unknown error'
            ),
            default => $this->content,
        };
    }

    protected function buildServiceRequestInstructions(): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        $instructions = <<<'EOT'

## Service Request Submission

You can help users submit service requests conversationally. Store all collected data in your conversation context until submission. Be flexible—users can change their mind anytime.

### State Management
- Track collected data using field_id as keys
- When you have all required data, proceed to clarifying questions
- If user cancels, forget all collected data immediately
- If unsure about a previously collected value, ask user to confirm
- Before submission, verify all required fields present—prompt for any missing

### Tool Usage
- For simple fields (text, number, email, yes/no): collect conversationally
- For complex fields (select, radio, address, phone, date, file): call `request_field_input(field_id)`
- You'll receive field responses as messages with structured `internal_content`

### Field Collection Behavior
- Start with required fields first
- For yes/no questions: ask naturally, accept variations (yes/sure/nope/yep/nah)
- If user provides multiple values at once, extract all applicable values
- Never make up or assume values—always ask if unclear
- Handle corrections naturally: "Actually, change the priority to high" → update stored value

### Clarifying Questions
- After collecting all required fields, you MUST generate exactly 3 clarifying questions
- Questions must be specific to BOTH the request type AND the data already collected
- Good: "What operating system are you using?", "When did this first occur?", "Have you tried restarting?"
- Bad: "Is there anything else?", "Can you provide more details?", "What else should we know?"
- Ask one question at a time, wait for answer before asking next
- Store each Q&A pair as you go
- If user says "skip" or "no more questions", stop asking and proceed with whatever answers collected
- These questions help both AI resolution and human agents understand the issue

### Context Awareness
- When waiting for field input, try to interpret response as field value first
- If user is clearly asking an unrelated question, answer it and maintain collected data
- Guide user back to request after answering tangential questions
- Distinguish between: field response, new question, correction, cancellation

### Submission
- Summarize all collected data before asking for confirmation
- Ask explicit confirmation: "Should I submit this request?"
- If validation fails, explain errors and re-collect only invalid fields
- Provide request number after successful submission
EOT;

        if ($aiResolutionSettings->is_enabled) {
            $instructions .= <<<'EOT'

### AI Resolution
- After clarifying questions, call `evaluate_ai_resolution` with the collected data
- Present solution only if confidence meets threshold
- Always ask "Did this resolve your issue?" and wait for explicit yes/no
- Record the outcome regardless of user's answer
- If user declines or confidence too low, proceed to submission—don't abandon the request
EOT;
        }

        return $instructions;
    }

    /**
     * @return array<int, Tool>
     */
    protected function buildTools(): array
    {
        if (! PortalAssistantServiceRequestFeature::active()) {
            return [];
        }

        return [
            new GetServiceRequestFormTool(),
            new SuggestServiceRequestTypeTool($this->thread),
            new RequestFieldInputTool($this->thread),
            new EvaluateAiResolutionTool(),
            new SubmitServiceRequestTool($this->thread),
        ];
    }
}
