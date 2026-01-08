# Portal Assistant Service Request Integration

## Overview

Enable conversational service request submission through the Portal Assistant. AI collects form data via chat (simple fields) or UI widgets (complex fields), then submits in a single call.

---

## Architecture

**Stateless:** AI maintains all state in conversation context. No backend drafts.

**5 Tools:** `suggest_service_request_type`, `get_service_request_form`, `request_field_input`, `evaluate_ai_resolution`, `submit_service_request`

**Field Collection Strategy:**
- Conversational: text, textarea, number, email, checkbox (yes/no)
- UI Widget: select, radio, address, phone, date, file

**Message Content Model:**
- `content`: Presentational text shown in chat
- `internal_content`: Structured data sent to LLM (validated server-side before adding to context)

---

## Key Files & Integration Points

### Message Handling

| Component | Location |
|-----------|----------|
| Message Model | `app-modules/ai/src/Models/PortalAssistantMessage.php` |
| Thread Model | `app-modules/ai/src/Models/PortalAssistantThread.php` |
| Send Controller | `app-modules/ai/src/Http/Controllers/PortalAssistant/SendMessageController.php` |
| Send Job | `app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php` |
| Message Event | `app-modules/ai/src/Events/PortalAssistant/PortalAssistantMessageChunk.php` |
| Channel Auth | `routes/channels.php:54-56` |

**Current flow:** Controller validates `content` → dispatches `SendMessage` job → job builds context, calls `streamRaw()` → broadcasts chunks to `portal-assistant-thread-{threadId}` channel.

**Required changes:**
- Add `internal_content` (json, nullable) column to `portal_assistant_messages`
- Validate `internal_content` in SendMessage job before adding to context
- Use `internal_content` instead of `content` when building AI context if present

### Service Requests

| Component | Location |
|-----------|----------|
| ServiceRequest Model | `app-modules/service-management/src/Models/ServiceRequest.php` |
| ServiceRequestType Model | `app-modules/service-management/src/Models/ServiceRequestType.php` |
| Form Model | `app-modules/service-management/src/Models/ServiceRequestForm.php` |
| Form Field Model | `app-modules/service-management/src/Models/ServiceRequestFormField.php` |
| Get Form Controller | `app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestFormController.php` |
| Store Controller | `app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreServiceRequestController.php` |
| Schema Generator | `app-modules/form/src/Actions/GenerateFormKitSchema.php` |

**Existing fields on ServiceRequest:** `is_ai_resolution_attempted`, `is_ai_resolution_successful`, `ai_resolution_confidence_score` (1-100)

**Existing settings:** `AiResolutionSettings` (`app-modules/ai/src/Settings/AiResolutionSettings.php`) has `is_enabled` and `confidence_threshold` (default 70)

### AI Service

| Component | Location |
|-----------|----------|
| AI Service Contract | `app-modules/ai/src/Services/Contracts/AiService.php` |
| OpenAI Implementation | `app-modules/integration-open-ai/src/Services/BaseOpenAiService.php` |
| Streaming Chunks | `app-modules/ai/src/Support/StreamingChunks/` |

**Key methods:** `streamRaw()` returns closure yielding `Text` and `Meta` chunks. Context passed via `prompt` param. Currently uses `withProviderOptions(['tools' => [...]])` for file_search/image_generation.

### Frontend (Portal)

| Component | Location |
|-----------|----------|
| Portal Config | `app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalController.php` |
| Echo Config | Returned in `websockets_config` from portal controller |
| API Routes | `app-modules/ai/routes/api.php` |

---

## User Experience Flow

### 1. Intent Recognition & Type Selection
- User expresses need (e.g., "I need password reset")
- Assistant analyzes intent (knowledge base question vs. service request)
- Calls `suggest_service_request_type(user_query)`
- Tool emits action to render type selector widget with AI suggestion + full category tree
- User selects type → type_id stored in conversation context
- **Cancellation:** User cancels widget → returns to general assistance

### 2. Form Schema Retrieval
- Assistant calls `get_service_request_form(type_id)`
- Receives field metadata: field_id, type, label, validation, config
- Plans collection strategy for each field

### 3. Field Collection

**Conversational collection:**
- Text/Textarea/Number/Email: Ask naturally, user responds in chat
- Checkbox (Yes/No): "Do you agree to terms?" → accept variations (yes/sure/nope/yep)
- Assistant stores responses in context using field_id as key

**UI widget collection:**
- Select/Radio/Address/Phone/Date/File: Call `request_field_input(field_id)`
- Tool emits action to render appropriate widget
- User fills → submits → frontend sends message with `internal_content`
- `internal_content` contains structured data: `{type: 'field_response', field_id: 'uuid', value: {...}}`
- `content` contains display text: "✓ Address: 123 Main St, LA, CA 90001"

**Multi-value extraction:**
- "I need password reset ASAP, high priority" → extract both title and priority
- Store both, avoid re-asking

### 4. Context Switching
- **User asks unrelated question:** Answer it, maintain collected data, guide back to request
- **User says "cancel":** Forget all collected data, return to general assistance
- **User starts different request:** Ask if they want to cancel current, then proceed accordingly
- Assistant must distinguish between field response vs. new question vs. context switch

### 5. Clarifying Questions
- Triggered after all required fields are collected
- Assistant generates exactly 3 questions (fixed count, not configurable)
- Questions must be specific to the request type AND the data already collected
- **Good examples:** "What operating system are you using?", "When did this issue first occur?", "Have you tried restarting the application?"
- **Bad examples:** "Is there anything else?", "Can you provide more details?", "What else should we know?"
- Ask one question at a time, wait for response before next
- Store as array: `[{question: "What OS?", answer: "Windows 11"}, ...]`
- If user says "skip", "no more questions", or similar → proceed to AI resolution with whatever answers collected
- Questions and answers are stored as ServiceRequestUpdate records on submission

### 6. AI Resolution Attempt
- Only runs if `AiResolutionSettings::is_enabled` is true
- Call `evaluate_ai_resolution(type_id, form_data, questions_answers)`
- Tool uses AI to analyze the request and generate a potential resolution
- AI self-rates its confidence in the resolution (0-100)
- Tool returns: `{resolvable: bool, confidence_score: int, proposed_answer: string, threshold: int}`
- **If `confidence_score >= threshold`:**
  - Present the proposed solution to user
  - Ask explicitly: "Did this resolve your issue?"
  - If user says yes → record `ai_resolution: {confidence_score, proposed_answer, user_accepted: true}`, may skip submission or still create record for tracking
  - If user says no → record `ai_resolution: {confidence_score, proposed_answer, user_accepted: false}`, proceed to submission
- **If `confidence_score < threshold` or AI resolution disabled:**
  - Skip resolution attempt entirely
  - Proceed directly to submission confirmation
- Resolution attempt data (if any) included in final submission payload for analytics

### 7. Submission
- Summarize all collected data for user review
- Ask explicit confirmation: "Should I submit this request?"
- User confirms → call `submit_service_request(payload)`
- Return request number on success
- **Validation errors:** Re-collect only invalid fields, resubmit

---

## Tool Specifications

### 1. suggest_service_request_type

**Prism:** `withStringParameter('user_query', '...')`
**Input:** `user_query: string`
**Output:** Emits `PortalAssistantActionRequest` event, returns acknowledgment string
**Data source:** `ServiceRequestType::with('category')` tree structure

### 2. get_service_request_form

**Prism:** `withStringParameter('type_id', '...')`
**Input:** `type_id: string`
**Output:** JSON string with `{type_id, type_name, category_path, fields: [{field_id, name, label, type, required, validation, options?, config?}]}`
**Data source:** Adapt `GetServiceRequestFormController` logic, transform FormKit schema to simplified format

### 3. request_field_input

**Prism:** `withStringParameter('field_id', '...')`
**Input:** `field_id: string`
**Output:** If simple type → `{needs_ui: false}`. If complex → emits `PortalAssistantActionRequest`, returns acknowledgment

### 4. evaluate_ai_resolution

**Prism:** `withStringParameter('type_id')`, `withObjectParameter('form_data', ...)`, `withArrayParameter('questions_answers', ...)`
**Input:** `type_id`, `form_data`, `questions_answers`
**Output:** JSON `{resolvable: bool, confidence_score: int (0-100), proposed_answer: string, threshold: int}`
**Logic:** AI self-rates confidence. Threshold from `AiResolutionSettings::confidence_threshold`. Skip if `AiResolutionSettings::is_enabled` is false.

### 5. submit_service_request

**Prism:** `withStringParameter('type_id')`, `withObjectParameter('form_data')`, `withArrayParameter('questions_answers')`, `withObjectParameter('ai_resolution', ..., required: [])`, `withArrayParameter('file_references', ..., required: [])`

**Input payload structure:**
- `type_id`: Service request type UUID
- `form_data`: Object keyed by field_id → value (simple values or complex objects like address)
- `questions_answers`: Array of `{question: string, answer: string}`
- `ai_resolution`: Optional `{confidence_score: int, proposed_answer: string, user_accepted: bool}`
- `file_references`: Optional array of `{path: string, name: string, size: int}`

**Output:** JSON `{success: bool, request_number: string, request_id: string, errors?: array, attached_files?: array}`

**Implementation:**
- Reuse `StoreServiceRequestController` logic
- Map field_id → field name (Main.title, CustomStep.uuid, etc.)
- Create ServiceRequest, ServiceRequestFormSubmission records
- Store questions as ServiceRequestUpdate records (plain text, not encrypted)
- Handle AI resolution data if provided
- Move files from temp to permanent storage
- Assign to team member
- Return errors array if validation fails (assistant re-collects invalid fields only)

---

## Prism Tool Integration

**Reference:** https://prismphp.com/core-concepts/tools-function-calling.html

### Tool Class Pattern

**Location:** Create `app-modules/ai/src/Tools/PortalAssistant/` directory

Each tool extends `Prism\Prism\Tool` and implements `__invoke()`:
- Constructor calls fluent methods: `->as('name')->for('description')->withStringParameter(...)->using($this)`
- `__invoke()` receives validated parameters, must return string (JSON-encoded for structured data)
- Use `Tool::make(ClassName::class)` for dependency injection

### Parameter Types

| Prism Method | Use For |
|--------------|---------|
| `withStringParameter(name, description)` | `user_query`, `type_id`, `field_id` |
| `withObjectParameter(name, desc, schemas[], required[])` | `form_data`, `ai_resolution` |
| `withArrayParameter(name, desc, itemSchema)` | `questions_answers`, `file_references` |
| `withNumberParameter(name, description)` | confidence scores |
| `withBooleanParameter(name, description)` | flags |

For nested objects, use `Prism\Prism\Schema\StringSchema`, `NumberSchema`, `BooleanSchema`, `ObjectSchema`, `ArraySchema`.

### Streaming with Tools

**Critical:** Must call `->withMaxSteps(n)` where n ≥ 2 to enable tool usage.

Modify `BaseOpenAiService::streamRaw()` to:
1. Accept optional `array $tools` parameter
2. Pass tools via `->withTools($tools)` (not `withProviderOptions`)
3. Set `->withMaxSteps(5)` (allow multiple tool calls per conversation turn)
4. Handle `ChunkType::ToolCall` in stream processing—emit new `ToolCall` chunk type
5. Tool results flow back automatically via Prism's step execution

### Tool Output Patterns

**Read-only tools** (get_service_request_form, evaluate_ai_resolution): Return JSON string directly.

**Action-emitting tools** (suggest_service_request_type, request_field_input):
1. Broadcast `PortalAssistantActionRequest` event
2. Return acknowledgment string for AI context (e.g., "Type selector displayed to user")

**Write tools** (submit_service_request): Return JSON with success/error details.

### Error Handling

Prism returns error messages to AI by default (no exceptions). For critical failures, use `->withoutErrorHandling()` on specific tools or catch in `__invoke()` and return structured error JSON.

### SendMessage Job Changes

Update `SendMessage` job to:
1. Instantiate tool classes: `$tools = [Tool::make(GetServiceRequestFormTool::class), ...]`
2. Pass to modified `streamRaw()`: `->streamRaw(..., tools: $tools)`
3. Add new `ToolCall` chunk type to handle tool execution feedback
4. Tools requiring thread context receive it via constructor injection

---

## internal_content Validation

**Location:** Create `app-modules/ai/src/Validators/InternalContentValidator.php`

**Message format:**
- `content`: Presentational text shown in chat (e.g., "✓ Address: 123 Main St, LA, CA 90001")
- `internal_content`: Structured data for LLM context

**internal_content types:**

`field_response`:
```
{type: 'field_response', field_id: 'uuid', value: {line1: '123 Main St', city: 'LA', state: 'CA', postal: '90001'}}
```
Validate: field_id exists in current form context, value matches field schema

`type_selection`:
```
{type: 'type_selection', type_id: 'uuid'}
```
Validate: type_id exists and is submittable

`widget_cancelled`:
```
{type: 'widget_cancelled', field_id: 'uuid'}
```
No validation needed—just signals user cancelled widget

`widget_error`:
```
{type: 'widget_error', field_id: 'uuid', error: 'Failed to load component'}
```
No validation needed—signals widget render failure

**Integration:** Call validator in `SendMessage` job before adding to context. Return 422 with field-specific errors if invalid. On success, store message with both `content` and `internal_content`. When building AI context, use `internal_content` if present.

---

## Action Request Event

**Create:** `app-modules/ai/src/Events/PortalAssistant/PortalAssistantActionRequest.php`

**Pattern:** Follow `PortalAssistantMessageChunk` structure. Broadcast to same channel. Include `action_type` and `params`.

**Action types:**
- `select_service_request_type`: params include `suggestion`, `types_tree`
- `render_field_input`: params include `field_id`, `block_definition`

---

## Frontend Components

**Location:** Create in portal's Vue components directory

**Required widgets:**
- TypeSelectorWidget: Shows AI suggestion highlighted + full category tree for browsing
- SelectBlock, RadioBlock: Render options from `blockDefinition.options`
- AddressBlock: Line1, Line2, City, State, Postal fields
- PhoneBlock: Country code + number with formatting
- DateBlock: Date picker (respect min/max from validation config)
- FileUploadBlock: Multi-file with progress, success/error states per file

**Shared interface:**
- Props: `fieldId`, `blockDefinition` (includes label, required, validation, options, config)
- Events: `submit(value, displayText)`, `cancel()`
- Must validate locally before submit
- Show loading state during server submission

**On submit:**
- POST to `/api/ai/portal-assistant/messages` with `content` (display text) and `internal_content` (structured data)
- Wait for server response before showing confirmation in chat
- Handle 422 errors: show inline error, allow correction and retry
- Handle network errors: show retry button

**On cancel:**
- Send message with `internal_content: {type: 'widget_cancelled', field_id: '...'}`
- Assistant acknowledges and continues conversation

**Event listener:**
- Subscribe to `PortalAssistantActionRequest` on `portal-assistant-thread-{threadId}` channel
- On `select_service_request_type`: render TypeSelectorWidget with params
- On `render_field_input`: render appropriate block component based on `blockDefinition.type`

---

## System Prompt Additions

Add to SendMessage job context building when service request tools are available:

**Identity & Purpose:**
- "You help users submit service requests conversationally"
- "Store all data in your conversation context until submission"
- "Be flexible—users can change their mind anytime"

**State Management:**
- Track collected data using field_id as keys
- When you have all required data, proceed to clarifying questions
- If user cancels, forget all collected data immediately
- If unsure about a previously collected value, ask user to confirm
- Before submission, verify all required fields present—prompt for any missing

**Tool Usage:**
- For simple fields (text, number, email, yes/no): collect conversationally
- For complex fields (select, radio, address, phone, date, file): call `request_field_input(field_id)`
- You'll receive field responses as messages with structured `internal_content`

**Field Collection Behavior:**
- Start with required fields first
- For yes/no questions: ask naturally, accept variations (yes/sure/nope/yep/nah)
- If user provides multiple values at once, extract all applicable values
- Never make up or assume values—always ask if unclear
- Handle corrections naturally: "Actually, change the priority to high" → update stored value

**Clarifying Questions:**
- After collecting all required fields, you MUST generate exactly 3 clarifying questions
- Questions must be specific to BOTH the request type AND the data already collected
- Good: "What operating system are you using?", "When did this first occur?", "Have you tried restarting?"
- Bad: "Is there anything else?", "Can you provide more details?", "What else should we know?"
- Ask one question at a time, wait for answer before asking next
- Store each Q&A pair as you go
- If user says "skip" or "no more questions", stop asking and proceed with whatever answers collected
- These questions help both AI resolution and human agents understand the issue

**AI Resolution:**
- After clarifying questions, call `evaluate_ai_resolution` if enabled
- Present solution only if confidence meets threshold
- Always ask "Did this resolve your issue?" and wait for explicit yes/no
- Record the outcome regardless of user's answer
- If user declines or confidence too low, proceed to submission—don't abandon the request

**Context Awareness:**
- When waiting for field input, try to interpret response as field value first
- If user is clearly asking an unrelated question, answer it and maintain collected data
- Guide user back to request after answering tangential questions
- Distinguish between: field response, new question, correction, cancellation

**Submission:**
- Summarize all collected data before asking for confirmation
- Ask explicit confirmation: "Should I submit this request?"
- If validation fails, explain errors and re-collect only invalid fields
- Provide request number after successful submission

---

## File Upload Handling

- Files upload to temp storage during conversation
- Widget shows clear success/failure per file with retry option
- Failed uploads excluded from payload (not silent)
- Submission tool moves successful files to permanent storage
- Response includes list of actually attached files

---

## Error Handling

**Validation errors (422):**
- Return `{message, errors, field_id}` from server
- Frontend shows error inline, allows retry
- Do not add message to conversation context on validation failure

**Tool errors:**
- Return `{error: true, message, recoverable: bool, suggestion?}` from tool
- AI explains issue in natural language
- Offer alternatives: retry, skip if optional, cancel request
- Never leave conversation in broken state

**Widget failures:**
- If widget fails to render, frontend sends error in `internal_content`
- Assistant falls back to conversational collection where possible
- For fields that require UI (file upload), explain the issue and suggest retry

**Context drift:**
- AI prompts for any data it's uncertain about before submission
- Long conversations may lose context—AI should verify critical fields before final submission

**Submission failures:**
- Return specific field errors from `submit_service_request`
- AI re-collects only the invalid fields
- Resubmit with corrected data

---

## Implementation Order

1. **Foundation:**
   - Migration: `internal_content` column on `portal_assistant_messages`
   - `InternalContentValidator` class
   - Update `SendMessage` job to validate/use `internal_content`
   - Modify `BaseOpenAiService::streamRaw()` to accept `$tools` array and set `withMaxSteps()`
   - Add `ToolCall` streaming chunk type

2. **Tools** (in `app-modules/ai/src/Tools/PortalAssistant/`):
   - `GetServiceRequestFormTool` (read-only, test Prism integration)
   - `SuggestServiceRequestTypeTool` (first action-emitting tool)
   - `RequestFieldInputTool`
   - `EvaluateAiResolutionTool`
   - `SubmitServiceRequestTool` (most complex, write operation)

3. **Frontend:**
   - `PortalAssistantActionRequest` event listener
   - TypeSelectorWidget
   - Field input components (AddressBlock, PhoneBlock, DateBlock, SelectBlock, RadioBlock)
   - FileUploadBlock

4. **Integration:**
   - System prompt additions for tool usage
   - End-to-end flow testing
   - Error handling verification

---

## Open Questions

1. Field response timeout before assuming context switch?
2. Authorization: per-tool or centralized? (Recommend per-tool)
3. Context window limits for long conversations?
4. Yes/no and select parsing flexibility—what variations to accept?
