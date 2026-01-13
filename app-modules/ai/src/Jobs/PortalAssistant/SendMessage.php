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
use AidingApp\Ai\Tools\PortalAssistant\CancelServiceRequestTool;
use AidingApp\Ai\Tools\PortalAssistant\CheckAiResolutionValidityTool;
use AidingApp\Ai\Tools\PortalAssistant\EnableFileAttachmentsTool;
use AidingApp\Ai\Tools\PortalAssistant\GetDraftStatusTool;
use AidingApp\Ai\Tools\PortalAssistant\GetServiceRequestTypesForSuggestionTool;
use AidingApp\Ai\Tools\PortalAssistant\RecordResolutionResponseTool;
use AidingApp\Ai\Tools\PortalAssistant\SaveClarifyingQuestionAnswerTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowFieldInputTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowTypeSelectorTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateDescriptionTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateFormFieldTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateTitleTool;
use AidingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\PortalAssistantServiceRequestFeature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Tool;
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
        Log::info('[PortalAssistant] User message received', [
            'thread_id' => $this->thread->getKey(),
            'user_content' => $this->content,
            'internal_content' => $this->internalContent,
            'request' => $this->request,
        ]);

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

            CRITICAL: During service request data collection, DO NOT search the knowledge base or provide troubleshooting suggestions unless the user explicitly asks. Focus ONLY on collecting required information. Knowledge base search is appropriate ONLY when:
            1. User explicitly asks a question about how to do something
            2. During clarifying questions stage to better understand their issue
            3. During resolution stage to provide helpful solutions
            During data collection, your ONLY job is to ask for and save the required information using the tools.

            CRITICAL: You MUST format ALL responses using Markdown. This is non-negotiable. Always use proper Markdown formatting. NEVER mention that you are responding using Markdown.
            EOT;

        if (PortalAssistantServiceRequestFeature::active()) {
            $context .= $this->buildServiceRequestInstructions();
        }

        try {
            $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

            $tools = $this->buildTools();

            Log::info('[PortalAssistant] Available tools for request', [
                'thread_id' => $this->thread->getKey(),
                'tools' => array_map(fn (Tool $tool) => [
                    'name' => $tool->name(),
                    'description' => $tool->description(),
                ], $tools),
            ]);

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
                    Log::info('[PortalAssistant] Tool called by AI', [
                        'thread_id' => $this->thread->getKey(),
                        'tool_name' => $chunk->name,
                        'tool_arguments' => $chunk->arguments,
                    ]);

                    continue;
                }

                if ($chunk instanceof ToolResult) {
                    Log::info('[PortalAssistant] Tool result', [
                        'thread_id' => $this->thread->getKey(),
                        'tool_name' => $chunk->toolName,
                        'tool_result' => $chunk->result,
                    ]);

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

            Log::info('[PortalAssistant] AI response complete', [
                'thread_id' => $this->thread->getKey(),
                'ai_content' => $response->content,
                'message_id' => $response->message_id,
            ]);
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

        $resolutionStage = $aiResolutionSettings->is_enabled
            ? <<<'EOT'
4. **Resolution** (draft_stage=resolution):
   - Based on everything collected, formulate a helpful resolution
   - Call `check_ai_resolution_validity` with confidence score and proposed answer
   - If confidence meets threshold: Present the resolution, ask if it helped, then call `record_resolution_response` to submit - this is the END
   - If confidence is too low: Inform user their request was submitted for review - this is the END
EOT
            : '';

        $afterClarifyingQuestions = $aiResolutionSettings->is_enabled
            ? ''
            : '   - After all 3 questions are answered, the request will be submitted for review';

        $priorityText = $aiResolutionSettings->is_enabled
            ? 'Follow the stages in order - collect data, ask clarifying questions, then attempt resolution.'
            : 'Follow the stages in order - collect data, ask clarifying questions, then the request submits for review.';

        $instructions = <<<EOT

## Service Request Submission

Help users submit service requests through natural conversation. Be brief. Ask ONE question at a time.

### Stages & Tools
1. **Type Selection** (no draft): `get_service_request_types_for_suggestion` → `show_type_selector`
2. **Data Collection** (draft_stage=data_collection):
   - Each field has a `collection_method` telling you how to collect it:
     - `"text"`: Ask question, then `update_form_field(field_id, value)`
     - `"show_field_input"`: `show_field_input(field_id)` AND ask question in same response
   - Description: `enable_file_attachments` first, then ask, then `update_description`
   - Title: Suggest a title, then `update_title`
3. **Clarifying Questions** (draft_stage=clarifying_questions):
   - Ask 3 questions to gather additional information you need to better understand their situation
   - These are NOT for re-collecting form data - ask about context, urgency, troubleshooting history, or any other relevant details
   - Examples: "When did this start?", "Have you tried anything already?", "Is this blocking your work?"
   - After each answer, call `save_clarifying_question_answer` with both question and answer
   - You will receive the already-collected form fields, title, and description in context - do NOT ask about these again
{$afterClarifyingQuestions}
{$resolutionStage}

### Key Rules
- Tool responses include `next_instruction` with exact prompts and tool calls - follow it
- Ask naturally, not robotically (e.g., "What's your Student ID?" not "Please provide Student ID field value")
- For optional fields, only ask if relevant to the conversation
- Call tools AFTER user responds, not before

### Priority
Once a service request draft is started, your #1 goal is to complete and submit it. Follow the stages in order - do NOT skip ahead. {$priorityText} If user wants to cancel, use `cancel_service_request`. If you lose track of progress, call `get_draft_status`.
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

        $tools = [];

        if ($draft) {
            // Get draft status always available first
            $tools[] = new GetDraftStatusTool($this->thread);

            // Phase-specific tools - progressively unlock as user completes steps
            // Users can always edit previously unlocked fields (go back)
            // but cannot access future steps until prerequisites are met (no skipping ahead)
            $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);

            match ($draftStage) {
                ServiceRequestDraftStage::DataCollection => $this->addDataCollectionTools($tools, $draft),
                ServiceRequestDraftStage::ClarifyingQuestions => $this->addClarifyingTools($tools),
                ServiceRequestDraftStage::Resolution => $this->addResolutionTools($tools, $aiResolutionSettings),
            };

            // Cancel always last
            $tools[] = new CancelServiceRequestTool($this->thread);
        } else {
            // When no draft exists, type selection tools are available
            $tools[] = new GetServiceRequestTypesForSuggestionTool($this->thread);
            $tools[] = new ShowTypeSelectorTool($this->thread);
        }

        return $tools;
    }

    /**
     * Add tools for data collection stage - progressively expose based on what's filled
     * The flow is: fields (if any) → description → title
     * Priority is selected with type, so no longer collected separately
     *
     * When all required fields are filled, GetDraftStatusTool will instruct AI to advance to clarifying_questions
     */
    protected function addDataCollectionTools(array &$tools, ServiceRequest $draft): void
    {
        $draft->load(['priority.type']);

        $hasCustomFields = $this->typeHasCustomFields($draft);

        // Step 1: Form field tools (if type has custom fields)
        if ($hasCustomFields) {
            $tools[] = new UpdateFormFieldTool($this->thread);
            $tools[] = new ShowFieldInputTool($this->thread);
        }

        // Step 2: After all required form fields filled (or immediately if no fields), description and file attachments become available
        if (! $hasCustomFields || $this->allRequiredFormFieldsFilled($draft)) {
            $tools[] = new UpdateDescriptionTool($this->thread);
            $tools[] = new EnableFileAttachmentsTool($this->thread);
        }

        // Step 3: After description filled, title becomes available
        if ($draft->close_details) {
            $tools[] = new UpdateTitleTool($this->thread);
        }

        // Auto-advance: When all required fields filled, AI calls get_draft_status which detects completion
        // and instructs AI to transition to clarifying_questions stage
    }

    /**
     * Check if the service request type has custom form fields
     */
    protected function typeHasCustomFields(ServiceRequest $draft): bool
    {
        $type = $draft->priority?->type;

        if (! $type) {
            return false;
        }

        $form = $type->form;

        if (! $form) {
            return false;
        }

        return ! empty($form->fields);
    }

    /**
     * Check if all required custom form fields have been filled
     */
    protected function allRequiredFormFieldsFilled(ServiceRequest $draft): bool
    {
        $type = $draft->priority?->type;

        if (! $type) {
            return false;
        }

        $form = $type->form;

        if (! $form) {
            return true; // No form means no required fields to check
        }

        $submission = $draft->serviceRequestFormSubmission;
        $filledFields = [];

        if ($submission) {
            $filledFields = $submission->fields()
                ->get()
                ->keyBy('id')
                ->map(fn ($field) => $field->pivot->response)
                ->all();
        }

        foreach ($form->fields as $field) {
            if ($field->is_required) {
                $fieldId = $field->getKey();
                $value = $filledFields[$fieldId] ?? null;

                if ($value === null || $value === '') {
                    return false;
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
        $tools[] = new SaveClarifyingQuestionAnswerTool($this->thread);

        // No other tools - SaveClarifyingQuestionAnswerTool handles auto-submission if resolution disabled
    }

    /**
     * Add tools for resolution phase
     *
     * @param mixed $aiResolutionSettings
     */
    protected function addResolutionTools(array &$tools, $aiResolutionSettings): void
    {
        if (! $aiResolutionSettings->is_enabled) {
            return;
        }

        $draft = null;

        if ($this->thread->current_service_request_draft_id) {
            $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
                ->where('id', $this->thread->current_service_request_draft_id)
                ->where('is_draft', true)
                ->first();
        }

        if (! $draft) {
            return;
        }

        $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);

        // CheckAiResolutionValidityTool is available during ClarifyingQuestions stage and Resolution stage (before submission)
        if (in_array($draftStage, [ServiceRequestDraftStage::ClarifyingQuestions, ServiceRequestDraftStage::Resolution])) {
            // Only add if resolution hasn't been submitted yet
            $hasResolutionSubmitted = $draft->serviceRequestUpdates()
                ->where('update_type', ServiceRequestUpdateType::AiResolutionSubmitted)
                ->exists();

            if (! $hasResolutionSubmitted) {
                $tools[] = new CheckAiResolutionValidityTool($this->thread);
            }
        }

        // RecordResolutionResponseTool is only available in Resolution stage after AI proposes resolution with sufficient confidence
        if ($draftStage === ServiceRequestDraftStage::Resolution) {
            $hasAiResolutionProposed = $draft->serviceRequestUpdates()
                ->where('update_type', ServiceRequestUpdateType::AiResolutionProposed)
                ->exists();

            if ($hasAiResolutionProposed && $draft->ai_resolution_confidence_score) {
                $threshold = $aiResolutionSettings->confidence_threshold;

                if ($draft->ai_resolution_confidence_score >= $threshold) {
                    $tools[] = new RecordResolutionResponseTool($this->thread);
                }
            }
        }
    }
}
