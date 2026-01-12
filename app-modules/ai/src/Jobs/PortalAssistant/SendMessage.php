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
use AidingApp\Ai\Tools\PortalAssistant\CheckAiResolutionValidityTool;
use AidingApp\Ai\Tools\PortalAssistant\FetchServiceRequestTypesTool;
use AidingApp\Ai\Tools\PortalAssistant\GetDraftStatusTool;
use AidingApp\Ai\Tools\PortalAssistant\RecordResolutionResponseTool;
use AidingApp\Ai\Tools\PortalAssistant\SaveClarifyingQuestionTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowFieldInputTool;
use AidingApp\Ai\Tools\PortalAssistant\ShowTypeSelectorTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateDescriptionTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateFormFieldTool;
use AidingApp\Ai\Tools\PortalAssistant\UpdateTitleTool;
use AidingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
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

            CRITICAL: You MUST format ALL responses using Markdown. This is non-negotiable. Always use proper Markdown formatting. NEVER mention that you are responding using Markdown.

            CRITICAL INTERACTION RULE: When collecting information from users, ask ONLY ONE question per message. Wait for their response before asking the next question. NEVER ask multiple questions in a single response. Do NOT use bold formatting for your questions.
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

        $instructions = <<<'EOT'

## Service Request Submission

CRITICAL: Keep ALL responses during service request submission brief and focused. Ask ONE question at a time. Do NOT explain next steps or the process unless requested to by the user.

You can help users submit service requests through natural conversation. The draft state is automatically saved—you don't need to track IDs or remember what's been collected.

### Getting Started
CRITICAL TWO-STEP PROCESS:
1. FIRST: Call `fetch_service_request_types` to get the list of available types and create a draft
   - This returns a `types_tree` with all available service request types
   - Each type has: `type_id` (UUID string), `name`, and `description`
   - The types_tree is hierarchical with categories containing types
   - Example structure: [{"category_id": "...", "name": "Technical Support", "types": [{"type_id": "abc-123", "name": "Password Reset", "description": "Help with password issues"}]}]
   - ANALYZE the user's request against ALL type names and descriptions
   - Look for BOTH keyword matches AND semantic matches
   - Examples of clear matches:
     * User: "my printer is broken" → Type: "Printer Issue" (description: "Report problems with printers...")
     * User: "password problem" → Type: "Password Reset" (description: "Help with password issues")
     * User: "wifi not working" → Type: "WiFi/Internet Issue" (description: "Report problems with wireless...")
   - If you find such a match, extract and remember that exact `type_id` UUID for the next step
   
2. SECOND: Call `show_type_selector` to display the type selection UI
   - CRITICAL: Analyze the types_tree to find a match BEFORE calling this tool
   - If you found a strong match: Pass the exact `type_id` UUID string as the `suggested_type_id` parameter
     Example: show_type_selector(suggested_type_id="d691de0b-c90d-44b0-aa2b-6e17cf0ea10c")
   - If NO clear match: Omit the parameter entirely - call show_type_selector() with NO parameters
   - NEVER pass an empty string, null, or any placeholder value
   - Response style: Your response should briefly acknowledge their request and either:
     * If suggesting a type: "I think this might be a [type name] request. Please confirm or select a different type."
     * If not suggesting: "Please select the type of request that best matches your issue."
   
3. THIRD: After user selects type and priority, you will receive an internal message. You MUST call `get_draft_status` FIRST before taking any other action or asking any questions.

### Collecting Information (Data Collection Phase)
CRITICAL RULES:
1. Ask for ONLY ONE piece of information per message
2. NEVER combine multiple questions in one response
3. After asking for information, STOP and wait for the user's response
4. When user provides information, save it with the appropriate tool, then call `get_draft_status` to see what to ask next

Collection Order (MUST follow this order):
1. **Custom form fields** (if the type has any) - Required fields first, then optionally collect helpful optional fields
2. **Description** - Ask for detailed description of the issue
3. **Title** - Suggest a title based on all information collected, user can accept/modify

IMPORTANT: Priority is selected WITH the type at the beginning, not collected separately afterward.

After type/priority selection:
- ALWAYS call `get_draft_status` FIRST to see what to collect
- `get_draft_status` will return an instruction telling you the field label to collect
- Ask the user for that information as a natural, conversational question
- For simple text fields: Ask the question and STOP. When they answer, call `update_form_field`
- For complex fields (selects, dates, etc.): Ask the question AND call `show_field_input` in the SAME response (do both actions together)
- Then ALWAYS call `get_draft_status` again to get the next instruction

CRITICAL: After ANY widget submission (form fields, type selection, etc.), you MUST call `get_draft_status` before asking any questions. This ensures you have the current state and don't ask for information that's already been provided.

Response style for questions:
- Form natural, conversational questions - don't just copy the field label
- Examples: 
  * Field label: "Printer Name/Location" → Ask: "What is the printer's name, and where is it?"
  * Field label: "Issue Type" → Ask: "What type of issue are you experiencing with the printer?"
- For select/radio/checkbox fields: Do NOT list available options in your question - the widget displays them
- Keep questions SHORT and conversational
- Do NOT use bold formatting for questions
- Ask only ONE question per message

Auto-transition to Clarifying Questions:
- When all required fields (fields/description/title) are filled, `get_draft_status` will automatically transition to `clarifying_questions` phase
- You will receive instruction to start asking the first clarifying question
- No separate submission tool needed

Optional Fields:
- After all REQUIRED fields are collected, `get_draft_status` may indicate optional fields are available
- Use your judgment: ask for optional fields ONLY if they would be genuinely helpful for resolving the user's issue
- You can collect 0, 1, or multiple optional fields - whatever makes sense for the context

### During Conversation
- After saving, call `get_draft_status` to see what to ask next
- If they correct themselves, update with the new value
- Never guess—always ask if unsure
- Keep responses SHORT: "Got it." then get_draft_status will tell you the next question

### Clarifying Questions
CRITICAL: Must ask EXACTLY 3 clarifying questions. Each question MUST draw from the information already collected to become more specific and relevant to the user's issue.

PURPOSE: Gather ADDITIONAL INFORMATION ONLY. Do NOT provide solutions, troubleshooting steps, or advice during this phase. Your ONLY job is to ask questions and save answers.

Rules:
- After successful data collection, ask exactly 3 clarifying questions ONE AT A TIME
- Each question should build on previously submitted answers (title, description, form fields, and previous Q&A)
- Make questions highly specific to their particular situation - NOT generic questions
- Examples:
  * BAD (generic): "What operating system are you using?"
  * GOOD (specific): "You mentioned the login error started yesterday - did anything change on your device before that?"
  * BAD (generic): "When did this start?"
  * GOOD (specific): "Is the 'Access Denied' error happening on all files or just specific ones?"
- IMMEDIATELY after user answers each question, call `save_clarifying_question(question="...", answer="...")` with BOTH the question you asked AND their answer
- Do NOT provide solutions or advice - just ask question, get answer, save it, repeat
- After 3rd question is saved, the system automatically handles next steps
EOT;

        if ($aiResolutionSettings->is_enabled) {
            $instructions .= <<<'EOT'

### AI Resolution
CRITICAL: Do NOT show any resolution to the user until `check_ai_resolution_validity` tells you to.

Process:
1. After 3rd question saved, call `check_ai_resolution_validity` with your confidence score (0-100) and proposed resolution
2. The tool checks if your confidence meets the configured threshold

**If confidence below threshold:**
- Tool automatically submits request for human review (no user interaction)
- Tool returns request number
- Tell user their request has been submitted and provide request number

**If confidence meets threshold:**
- Tool instructs you to present resolution to user
- Show the resolution and ask: "Did this solve your problem?"
- Wait for explicit yes/no response
- Call `record_resolution_response` with their answer
- Tool automatically submits request and returns request number
- If accepted: Tell user issue is resolved, provide request number
- If rejected: Tell user request submitted for human review, provide request number
EOT;
        } else {
            $instructions .= <<<'EOT'

### Automatic Submission
- AI resolution is disabled
- After 3rd clarifying question is saved, request automatically submitted for human review
- Tool returns request number - provide it to user
EOT;
        }

        $instructions .= <<<'EOT'


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

        // Always available tools - users can restart or change type at any time
        $tools = [
            new FetchServiceRequestTypesTool($this->thread),
            new ShowTypeSelectorTool($this->thread),
            new GetDraftStatusTool($this->thread),
        ];

        // Phase-specific tools - progressively unlock as user completes steps
        // Users can always edit previously unlocked fields (go back)
        // but cannot access future steps until prerequisites are met (no skipping ahead)
        if ($draft) {
            $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);

            match ($draftStage) {
                ServiceRequestDraftStage::DataCollection => $this->addDataCollectionTools($tools, $draft),
                ServiceRequestDraftStage::ClarifyingQuestions => $this->addClarifyingTools($tools),
                ServiceRequestDraftStage::Resolution => $this->addResolutionTools($tools, $aiResolutionSettings),
            };
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

        // Step 2: After all required form fields filled (or immediately if no fields), description becomes available
        if (! $hasCustomFields || $this->allRequiredFormFieldsFilled($draft)) {
            $tools[] = new UpdateDescriptionTool($this->thread);
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

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

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
        $type = $draft->priority?->type;

        if (! $type) {
            return false;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

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
        
        // No other tools - SaveClarifyingQuestionTool handles auto-submission if resolution disabled
    }

    /**
     * Add tools for resolution phase
     */
    protected function addResolutionTools(array &$tools, $aiResolutionSettings): void
    {
        $draft = null;
        if ($this->thread->current_service_request_draft_id) {
            $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
                ->where('id', $this->thread->current_service_request_draft_id)
                ->where('is_draft', true)
                ->first();
        }

        if ($aiResolutionSettings->is_enabled && $draft) {
            // CheckAiResolutionValidityTool is only available after 3 questions are saved
            $clarifyingQuestionsCount = $draft->serviceRequestUpdates()
                ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
                ->count();

            if ($clarifyingQuestionsCount === 3) {
                $tools[] = new CheckAiResolutionValidityTool($this->thread);
            }

            // RecordResolutionResponseTool is only available after AI proposes resolution with sufficient confidence
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

        // No SubmitServiceRequestTool - resolution tools handle submission internally
    }
}

