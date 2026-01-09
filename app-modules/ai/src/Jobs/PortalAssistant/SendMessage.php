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
use Illuminate\Support\Facades\Log;
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
            - Communicate naturally with users as a human would - never explain your internal processes, tool calls, or data structures
            - Never mention technical details like field_ids, JSON, internal state, or how you're organizing information
            - Keep responses conversational and focused on helping the user
            
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
                    // Only log tool calls that have results (after execution)
                    if ($chunk->result !== null) {
                        Log::info('Portal Assistant: Tool executed', [
                            'thread_id' => $this->thread->getKey(),
                            'tool_name' => $chunk->name,
                            'arguments' => $chunk->arguments,
                            'result_preview' => is_string($chunk->result) ? substr($chunk->result, 0, 200) : $chunk->result,
                        ]);
                    }

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

You can help users submit service requests through natural conversation. Be flexible—users can change their mind anytime.

### Getting Started
- When user wants to submit a request or report an issue, use `suggest_service_request_type` tool with their description
- After they select a type, use `get_service_request_form` tool to learn what information is needed
- Remember information users provide throughout the conversation
- If user wants to cancel, discard everything and start fresh

### Field Collection
- For simple questions (text, numbers, email, yes/no): ask naturally in conversation
- For complex selections (dropdowns, dates, file uploads, addresses, phone numbers): use `request_field_input` tool with the field_id
- Start with required information first
- For yes/no questions: accept natural answers (yes/sure/no/nope/yep/nah - they all count)
- If they give you multiple pieces of information at once, use all of it
- Never guess—always ask if you're not sure about something
- If they correct themselves ("Actually, make it high priority"), update your understanding
- Before submitting, make sure you have all required information—ask for anything missing

### Clarifying Questions
- After you have all the required information, ask 3 relevant follow-up questions to better understand their situation
- Make questions specific to their issue and what they've told you
- Good examples: "What operating system are you using?", "When did this first occur?", "Have you tried restarting?"
- Bad examples: "Anything else?", "More details?", "What else?" (too vague)
- Ask one question at a time and wait for their answer
- Remember their answers
- If they say "skip" or "no more questions", that's fine—just move forward with what you have
- These questions help staff assist them better

### During Conversation
- Try to understand responses in context of what you're asking
- If they ask an unrelated question, answer it and keep their information
- Gently guide them back to their request after answering side questions
- Be able to tell the difference between: answering your question, asking something new, correcting information, or canceling

### Submission
- Before submitting, show them a summary of what you've collected
- Ask clearly: "Should I submit this request?"
- Once confirmed, use `submit_service_request` tool with all the data (type_id, priority_id, title, description, form_data as JSON, questions_answers as JSON, and ai_resolution if applicable)
- If there are any problems, explain what's wrong and collect just the problematic information again
- After submission succeeds, give them their request number
EOT;

        if ($aiResolutionSettings->is_enabled) {
            $instructions .= <<<'EOT'

### AI Resolution
- After the clarifying questions, use `evaluate_ai_resolution` tool to check if you can help resolve their issue
- Only suggest a solution if you're confident it will help
- Always ask "Did this resolve your issue?" and wait for a clear yes or no
- Remember whether your solution worked
- If they say no or you're not confident enough, continue with submitting their request—don't just abandon it
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
