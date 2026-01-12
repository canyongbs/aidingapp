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
use AidingApp\Ai\Support\StreamingChunks\Finish;
use AidingApp\Ai\Support\StreamingChunks\Meta;
use AidingApp\Ai\Support\StreamingChunks\Text;
use AidingApp\Ai\Support\StreamingChunks\ToolCall;
use AidingApp\Ai\Tools\PortalAssistant\FetchServiceRequestTypesTool;
use AidingApp\Ai\Tools\PortalAssistant\FinalizeServiceRequestTool;
use AidingApp\Ai\Tools\PortalAssistant\GetDraftStatusTool;
use AidingApp\Ai\Tools\PortalAssistant\RecordResolutionResponseTool;
use AidingApp\Ai\Tools\PortalAssistant\SaveClarifyingQuestionTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowFieldInputTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowPrioritySelectorTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowTypeSelectorTool;
use AidingApp\Ai\Tools\PortalAssistant\SubmitAiResolutionTool;
use AidingApp\Ai\Tools\PortalAssistant\SubmitServiceRequestTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateDescriptionTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateFormFieldTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateTitleTool;
use AidingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
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
use Prism\Prism\ValueObjects\Messages\UserMessage;
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

            CRITICAL INTERACTION RULE: When collecting information from users, ask ONLY ONE question per message. Wait for their response before asking the next question. NEVER ask multiple questions in a single response. Do NOT use bold formatting for your questions.
            EOT;

        if (PortalAssistantServiceRequestFeature::active()) {
            $context .= $this->buildServiceRequestInstructions();
        }

        try {
            $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

            $tools = $this->buildTools();

            $nextRequestOptions = $this->thread->messages()->where('is_assistant', true)->latest()->value('next_request_options') ?? [];

            $messages = [
                ...(filled($this->internalContent) ? [new DeveloperMessage($this->internalContent)] : []),
                new UserMessage($this->content),
            ];

            $stream = $aiService->streamRaw(
                prompt: $context,
                files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
                options: $nextRequestOptions,
                tools: $tools,
                messages: $messages,
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

                if ($chunk instanceof Finish) {
                    if ($chunk->error) {
                        Log::error('Portal Assistant: Stream finished with error', [
                            'thread_id' => $this->thread->getKey(),
                            'error' => $chunk->error,
                            'finishReason' => $chunk->finishReason?->name,
                        ]);
                    }

                    // Don't break yet - continue processing to ensure all chunks are handled
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

    protected function buildServiceRequestInstructions(): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        $instructions = <<<'EOT'

## Service Request Submission

CRITICAL: Keep ALL responses during service request submission brief and focused. Ask ONE question at a time. Do NOT explain next steps or the process unless requested to by the user.

You can help users submit service requests through natural conversation. The draft state is automatically saved—you don't need to track IDs or remember what's been collected.

### Getting Started
- When user wants to submit a request, report an issue, or speak to a human, use `fetch_service_request_types` to get available types and create a draft
- Then use `show_type_selector` to display the type selection UI (optionally with a suggested type_id based on their description)
- Response style: After calling `show_type_selector`, your response should be minimal - just ask them to select a type. Example: "Please select the type of request that best matches your issue."

### Collecting Information (Data Collection Phase)
CRITICAL RULES:
1. Ask for ONLY ONE piece of information per message
2. NEVER combine multiple questions in one response
3. After asking for information, STOP and wait for the user's response
4. When user provides information, save it with the appropriate tool, then call `get_draft_status` to see what to ask next

Collection Order:
- After type selection, the system will tell you to call `get_draft_status`
- `get_draft_status` will return an instruction telling you exactly what to ask for
- Ask the user that ONE question, then STOP
- When user responds, save their answer with the appropriate update tool
- Then call `get_draft_status` again to get the next instruction
- Response style: Just ask ONE question. Nothing else. Do NOT use bold formatting for questions.

### During Conversation
- After saving, call `get_draft_status` to see what to ask next
- If they correct themselves, update with the new value
- Never guess—always ask if unsure
- Keep responses SHORT: "Got it." then get_draft_status will tell you the next question

### Submission
- When all required fields are filled, use `submit_service_request` to validate
- If validation fails, ask for the missing field briefly: "I need the [field name]."

### Clarifying Questions
- After successful submission validation, ask exactly 3 clarifying questions
- Ask ONE question at a time, wait for answer, then save with `save_clarifying_question`
- Make questions specific and brief: "What operating system are you using?"
- Don't explain that these are "clarifying questions" - just ask them naturally
EOT;

        if ($aiResolutionSettings->is_enabled) {
            $instructions .= <<<'EOT'

### AI Resolution
- After 3 clarifying questions, try to resolve the issue using `submit_ai_resolution`
- Present your solution clearly but concisely
- Ask: "Did this solve your problem?"
- Use `record_resolution_response` to record their answer
- If confidence is below threshold: use `finalize_service_request` to submit for human review
EOT;
        } else {
            $instructions .= <<<'EOT'

### Finalization
- After 3 clarifying questions, use `finalize_service_request` to submit for human review
EOT;
        }

        $instructions .= <<<'EOT'

### After Finalization
- Give the user their request number
- Brief confirmation: "Your request [number] has been submitted. Our team will review it shortly."

REMEMBER: Speed and brevity are CRITICAL during service request submission. One question at a time. No explanations of the process.
EOT;

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

        $aiResolutionSettings = app(AiResolutionSettings::class);

        // Get current draft from thread's pointer
        $draft = null;
        if ($this->thread->current_service_request_draft_id) {
            $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
                ->where('id', $this->thread->current_service_request_draft_id)
                ->where('is_draft', true)
                ->first();
        }

        // Always available tools - users can restart or get status at any time
        $tools = [
            new FetchServiceRequestTypesTool($this->thread),
            new ShowTypeSelectorTool($this->thread),
            new GetDraftStatusTool($this->thread),
        ];

        // Phase-specific tools - progressively unlock as user completes steps
        // Users can always edit previously unlocked fields (go back)
        // but cannot access future steps until prerequisites are met (no skipping ahead)
        if ($draft) {
            $phase = $draft->workflow_phase;

            match ($phase) {
                'data_collection' => $this->addDataCollectionTools($tools, $draft),
                'clarifying_questions' => $this->addClarifyingTools($tools),
                'resolution' => $this->addResolutionTools($tools, $aiResolutionSettings),
                default => null,
            };
        }

        return $tools;
    }

    /**
     * Add tools for data collection phase - progressively expose based on what's filled
     * The flow is UNIFIED regardless of whether the type has custom form fields:
     * fields (if any) → description → title → priority
     *
     * Users can always go back and edit previous fields, but can't skip ahead
     */
    protected function addDataCollectionTools(array &$tools, ServiceRequest $draft): void
    {
        $draft->load(['priority', 'priority.type']);

        $hasCustomFields = $this->typeHasCustomFields($draft);

        // Step 1: Form field tools (if type has custom fields)
        if ($hasCustomFields) {
            $tools[] = new UpdateFormFieldTool($this->thread);
            $tools[] = new ShowFieldInputTool($this->thread);
        }

        // Step 2: After all required form fields filled (or immediately if no fields), description becomes available
        if (! $hasCustomFields || $this->allRequiredFormFieldsFilled($draft)) {
            $tools[] = new UpdateDescriptionTool($this->thread);
        }

        // Step 3: After description filled, title becomes available
        // AI will suggest title in response text, user confirms/edits, then AI calls update_title
        // Note: description is stored in close_details field
        if ($draft->close_details) {
            $tools[] = new UpdateTitleTool($this->thread);
        }

        // Step 4: After title saved, priority becomes available
        if ($draft->title) {
            $tools[] = new ShowPrioritySelectorTool($this->thread);
        }

        // Always allow submission attempt (validation will catch missing fields)
        $tools[] = new SubmitServiceRequestTool($this->thread);
    }

    /**
     * Check if the service request type has custom form fields
     */
    protected function typeHasCustomFields(ServiceRequest $draft): bool
    {
        if (! $draft->priority?->type) {
            return false;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($draft->priority->type, $uploadsMediaCollection);

        foreach ($form->steps as $step) {
            if ($step->label === 'Main' || $step->label === 'Questions') {
                continue;
            }

            if (! empty($step->fields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all required custom form fields have been filled
     */
    protected function allRequiredFormFieldsFilled(ServiceRequest $draft): bool
    {
        if (! $draft->priority?->type) {
            return false;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($draft->priority->type, $uploadsMediaCollection);

        $submission = $draft->serviceRequestFormSubmission;
        $filledFields = [];

        if ($submission) {
            $filledFields = $submission->fields()
                ->get()
                ->keyBy('id')
                ->map(fn ($field) => $field->pivot->response)
                ->all();
        }

        foreach ($form->steps as $step) {
            if ($step->label === 'Main' || $step->label === 'Questions') {
                continue;
            }

            foreach ($step->fields as $field) {
                if ($field->is_required) {
                    $fieldId = $field->getKey();
                    $value = $filledFields[$fieldId] ?? null;

                    if ($value === null || $value === '') {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Add tools for clarifying questions phase
     */
    protected function addClarifyingTools(array &$tools): void
    {
        $tools[] = new SaveClarifyingQuestionTool($this->thread);
        $tools[] = new FinalizeServiceRequestTool($this->thread);
    }

    /**
     * Add tools for resolution phase
     */
    protected function addResolutionTools(array &$tools, $aiResolutionSettings): void
    {
        if ($aiResolutionSettings->is_enabled) {
            $tools[] = new SubmitAiResolutionTool($this->thread);
            $tools[] = new RecordResolutionResponseTool($this->thread);
        }

        $tools[] = new FinalizeServiceRequestTool($this->thread);
    }
}

