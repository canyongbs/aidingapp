# Portal Assistant Service Request - Stateful Draft Implementation

## Problem Statement

The current implementation is stateless - all 5 tools require the AI to remember and pass IDs, and data is only persisted at final submission. This causes:
- AI forgets which fields are filled
- IDs and workflow state are lost
- Validation errors require re-collecting all data
- Too much responsibility on LLM memory

## Solution: is_draft Field on ServiceRequest

Add an `is_draft` boolean field to ServiceRequest with a global scope that excludes drafts by default. The draft is linked to the `PortalAssistantThread` and all tools operate on the active draft implicitly - no ID passing required.

---

## Workflow Summary

1. User expresses intent ("I want to submit a ticket", "I want to speak to a human", "I want to submit a service request")
2. AI calls `fetch_service_request_types` → creates draft, returns available types
3. AI calls `show_type_selector` with optional suggestion → UI widget appears
4. User selects type → backend updates draft, creates form submission
5. AI collects **title** and **description** via natural language (calling `update_title`, `update_description`)
6. AI calls `get_draft_status` to retrieve form structure for the selected type
7. AI collects form fields:
   - Text fields: conversationally via `update_form_field`
   - Complex fields (select, date, address, phone, file): via `show_field_input` → UI widget
8. AI calls `submit_service_request` → validates required fields
9. AI asks exactly 3 clarifying questions, saving each via `save_clarifying_question`
10. AI attempts resolution via `submit_ai_resolution` with confidence score
11. If confidence meets threshold: present to user, record response via `record_resolution_response`
12. If below threshold or user declines: call `finalize_service_request`

---

## Database Changes

### Migration: Add columns to `service_requests`

```php
$table->boolean('is_draft')->default(false)->index();
$table->foreignUuid('portal_assistant_thread_id')->nullable()->unique();
$table->string('workflow_phase')->nullable();
$table->json('clarifying_questions')->default('[]');
$table->json('ai_resolution')->nullable();
```

### Global Scope: ExcludeDraftServiceRequests

Add to ServiceRequest model's `booted()`:

```php
static::addGlobalScope('excludeDrafts', function (Builder $builder) {
    $builder->where('is_draft', false);
});
```

When working with drafts in the portal assistant, use:
```php
ServiceRequest::withoutGlobalScope('excludeDrafts')
    ->where('portal_assistant_thread_id', $thread->id)
    ->first();
```

When finalizing a draft:
```php
$draft->is_draft = false;
$draft->workflow_phase = null;
$draft->save();
```

---

## Workflow Phases (stored on ServiceRequest)

1. `type_selection` - Draft created, waiting for type selection
2. `data_collection` - Type selected, collecting title/description/fields
3. `submitted` - Required fields collected, validated, moved to enrichment
4. `clarifying_questions` - Collecting exactly 3 Q&A pairs
5. `resolution` - Attempting AI resolution

When finalized, `is_draft` is set to `false` and `workflow_phase` is cleared. The status is set to the default "New" (Open) status.

---

## Model Relationships

### PortalAssistantThread

Add relationship to draft ServiceRequest:

```php
public function draft(): HasOne
{
    return $this->hasOne(ServiceRequest::class, 'portal_assistant_thread_id')
        ->withoutGlobalScope('excludeDrafts');
}
```

### ServiceRequest

Add relationship back to thread:

```php
public function portalAssistantThread(): BelongsTo
{
    return $this->belongsTo(PortalAssistantThread::class);
}
```

---

## Tool Definitions

All tools receive `PortalAssistantThread` in constructor. Draft is resolved via `$this->thread->draft`.

### 1. `fetch_service_request_types`

**When called:** User expresses intent to create a service request (e.g., "I want to submit a ticket", "I want to speak to a human", "I need help with something")

**Parameters:** none

**Behavior:**
- Creates draft ServiceRequest if none exists for thread
- Draft has: is_draft=true, workflow_phase=type_selection, respondent=thread author
- Returns available types tree

**Returns:**
```json
{
  "types_tree": [...],
  "draft_created": true,
  "workflow_phase": "type_selection"
}
```

### 2. `show_type_selector`

**When called:** After fetching types, to display selection UI

**Parameters:**
- `suggested_type_id` (string, optional) - AI's best match based on conversation. If AI cannot confidently suggest a type, omit this parameter.

**Behavior:**
- Emits `PortalAssistantActionRequest` with action `select_service_request_type`
- UI shows types with suggestion highlighted/preselected (if provided)
- User can still choose any type regardless of suggestion

**Returns:** `"Type selector displayed. Wait for user selection."`

**Frontend handling:**
- When widget appears: **hide the main chat input** to avoid confusion about which field to use
- Show small "Cancel" or "Send message instead" button below widget to return to chat input
- User selects type → sends message with `internal_content: { type: 'type_selection', type_id: '...' }`
- User cancels → sends `internal_content: { type: 'widget_cancelled', widget_type: 'type_selector' }`, restores chat input
- Backend processes and updates draft: sets type, gets default priority, creates form submission, sets phase to `data_collection`

### 3. `get_draft_status`

**When called:**
- After type selection to retrieve the form structure for the selected type
- When resuming a conversation to restore context
- At any point to check what fields are filled/missing

**Parameters:** none

**Behavior:**
- Returns current draft state including form fields for the **current type only**
- Form fields that belonged to a previous type are not returned (but remain stored internally)

**Returns:**
```json
{
  "has_draft": true,
  "workflow_phase": "data_collection",
  "type_id": "uuid",
  "type_name": "Technical Support",
  "priority_id": "uuid",
  "priority_name": "Normal",
  "priorities": [{"id": "...", "name": "High"}, ...],
  "title": "Password reset needed",
  "description": null,
  "form_fields": [
    {
      "field_id": "uuid",
      "label": "Operating System",
      "type": "select",
      "required": true,
      "value": null,
      "filled": false,
      "options": [...]
    }
  ],
  "missing_required": ["description", "field_uuid_1"],
  "clarifying_questions": {"completed": 0, "required": 3},
  "can_submit": false
}
```

### 4. `update_title`

**When called:** When AI collects title conversationally. Can be called multiple times if user rephrases.

**Parameters:**
- `title` (string, required)

**Returns:**
```json
{
  "success": true,
  "title": "Password reset needed"
}
```

### 5. `update_description`

**When called:** When AI collects description conversationally. Can be called multiple times if user rephrases.

**Parameters:**
- `description` (string, required)

**Returns:**
```json
{
  "success": true,
  "description": "I cannot log in to my account..."
}
```

### 6. `update_form_field`

**When called:** When AI collects a text-based custom field conversationally. Can be called multiple times to update the value.

**Parameters:**
- `field_id` (string, required)
- `value` (string, required)

**Behavior:**
- Validates field exists for current type
- Saves to ServiceRequestFormSubmission field pivot
- Returns error if field doesn't belong to current type

**Returns:**
```json
{
  "success": true,
  "field_id": "uuid",
  "label": "Additional Notes"
}
```

### 7. `update_priority`

**When called:** When AI collects or changes priority

**Parameters:**
- `priority_id` (string, required)

**Returns:**
```json
{
  "success": true,
  "priority_name": "High"
}
```

### 8. `show_field_input`

**When called:** For complex fields that require structured input (select, radio, date, phone, address, file uploads). The AI must NOT attempt to collect these via chat.

**Parameters:**
- `field_id` (string, required)

**Behavior:**
- Validates field exists for current type
- Emits `PortalAssistantActionRequest` with action `render_field_input`
- UI renders appropriate input component above the chat

**Returns:** `"Input displayed for [Field Label]. Wait for user to complete it."`

**Frontend handling:**
- When widget appears: **hide the main chat input** to avoid confusion about which field to use
- Show small "Cancel" or "Send message instead" button below widget to return to chat input
- User fills and submits → sends message with `internal_content: { type: 'field_response', field_id: '...', value: {...} }`
- User cancels → sends `internal_content: { type: 'widget_cancelled', field_id: '...' }`, restores chat input
- Backend saves value directly to form submission field pivot - value does NOT return to AI as free text
- File uploads saved via existing media collection mechanism

### 9. `submit_service_request`

**When called:** After all required fields are filled (title, description, and all required custom fields)

**Parameters:** none

**Behavior:**
- Validates all required fields present
- If valid: sets phase to `clarifying_questions`
- If invalid: returns specific errors so AI can continue collecting missing fields

**Returns (success):**
```json
{
  "success": true,
  "workflow_phase": "clarifying_questions",
  "clarifying_questions_required": 3,
  "instruction": "Ask the user 3 clarifying questions specific to their request. Questions should help understand the issue better."
}
```

**Returns (validation failure):**
```json
{
  "success": false,
  "errors": {
    "description": "Description is required",
    "field_uuid": "This field is required"
  },
  "missing_required": ["description", "field_uuid"]
}
```

### 10. `save_clarifying_question`

**When called:** After user answers each clarifying question. Must be called exactly 3 times.

**Parameters:**
- `question` (string, required) - The question asked
- `answer` (string, required) - User's response

**Behavior:**
- Appends to `clarifying_questions` JSON array on ServiceRequest
- When 3 complete: sets phase to `resolution`

**Returns:**
```json
{
  "success": true,
  "completed": 2,
  "remaining": 1
}
```

Or when all 3 complete:
```json
{
  "success": true,
  "completed": 3,
  "remaining": 0,
  "workflow_phase": "resolution",
  "ai_resolution_enabled": true,
  "instruction": "Attempt to resolve the request using available knowledge. If confident, call submit_ai_resolution with your confidence score and proposed answer."
}
```

### 11. `submit_ai_resolution`

**When called:** After clarifying questions, when AI has a potential resolution

**Parameters:**
- `confidence_score` (integer 0-100, required) - AI's self-rated confidence that this resolution will help
- `proposed_answer` (string, required) - The resolution text

**Behavior:**
- Stores resolution data in `ai_resolution` JSON column
- Compares against configured threshold from AiResolutionSettings
- Returns whether AI should present the resolution to user

**Returns (meets threshold):**
```json
{
  "threshold": 70,
  "confidence_score": 85,
  "meets_threshold": true,
  "instruction": "Present your resolution to the user and ask if it solved their problem. Wait for explicit yes/no response."
}
```

**Returns (below threshold):**
```json
{
  "threshold": 70,
  "confidence_score": 45,
  "meets_threshold": false,
  "instruction": "Confidence too low. Do not show resolution to user. Call finalize_service_request to submit for human review."
}
```

### 12. `record_resolution_response`

**When called:** After user responds to a presented resolution (only called if confidence met threshold)

**Parameters:**
- `accepted` (boolean, required) - Whether user said the resolution solved their problem

**Behavior:**
- Updates `ai_resolution.user_accepted`
- Sets `is_ai_resolution_attempted` = true
- Sets `is_ai_resolution_successful` = accepted
- Finalizes the service request internally
- If accepted: marks as resolved/closed (still creates complete record)
- If rejected: submits for human handling

**Returns:**
```json
{
  "success": true,
  "request_number": "SR-2024-001234",
  "resolution_accepted": true,
  "status": "resolved"
}
```

### 13. `finalize_service_request`

**When called:** When resolution is skipped, below threshold, or user declined the resolution

**Parameters:** none

**Behavior:**
1. Creates ServiceRequestUpdate records for each clarifying Q&A
2. If AI resolution was attempted: stores resolution data, creates update records
3. Assigns to team member based on type's assignment strategy
4. Sets `is_draft` to false, sets status to Open/New
5. Clears `workflow_phase`
6. Generates `service_request_number`

**Returns:**
```json
{
  "success": true,
  "request_number": "SR-2024-001234",
  "message": "Service request submitted. A team member will review your request."
}
```

---

## Internal Content Processing

When `SendMessage` job receives a message with `internal_content`, process based on type before adding to AI context:

### `type_selection`
```json
{"type": "type_selection", "type_id": "uuid"}
```
- Validate type exists and has form
- If type is changing and form submission exists for old type:
  - Keep existing ServiceRequestFormSubmission (field values preserved internally)
  - Create new ServiceRequestFormSubmission for new type
  - Link new submission to draft
- If no form submission exists:
  - Create ServiceRequestFormSubmission for selected type
- Set default priority for new type
- Set `workflow_phase` to `data_collection`
- Note: If user switches back to previous type, old form submission data could be restored

### `field_response`
```json
{"type": "field_response", "field_id": "uuid", "value": {...}}
```
- Validate field belongs to current type's form
- Save via ProcessServiceRequestSubmissionField action
- For files: use existing media collection handling
- Value is stored directly - does not pass through AI as free text

### `widget_cancelled`
```json
{"type": "widget_cancelled", "widget_type": "type_selector" | "field_input", "field_id?": "uuid"}
```
- No data change
- Restores chat input
- AI should acknowledge and continue conversation

---

## Frontend Widget Behavior

When any widget (type selector, field input) is active:

1. **Hide main chat input** - Replace the text input area with the widget
2. **Show escape option** - Below the widget, show a small link/button: "Cancel" or "Send message instead"
3. **On widget submit** - Send message with `internal_content`, restore chat input
4. **On cancel/escape** - Send cancellation `internal_content`, restore chat input, AI acknowledges and continues

This prevents user confusion about which input field to use while still allowing the user to cancel, pivot, or clarify at any time.

### Frontend Files to Update
- `portals/knowledge-management/src/Components/Assistant/ChatInput.vue` - Add widget visibility toggle
- `portals/knowledge-management/src/Components/Assistant/AssistantWidget.vue` - Add cancel button, emit cancel event
- `portals/knowledge-management/src/Composables/assistant/useAssistantChat.js` - Handle widget state and cancellation

---

## Files to Modify

### Models
- `app-modules/service-management/src/Models/ServiceRequest.php`
  - Add global scope for excluding drafts (`where('is_draft', false)`)
  - Add `is_draft`, `portal_assistant_thread_id`, `workflow_phase`, `clarifying_questions`, `ai_resolution` to fillable/casts
  - Add `portalAssistantThread()` relationship
  - Add helper method: `isDraft()`

- `app-modules/ai/src/Models/PortalAssistantThread.php`
  - Add `draft()` relationship

### Migration
- Create migration adding columns to service_requests table (`is_draft`, `portal_assistant_thread_id`, `workflow_phase`, `clarifying_questions`, `ai_resolution`)

### Tools (replace existing in `app-modules/ai/src/Tools/PortalAssistant/`)
Delete existing tools and create:
- `FetchServiceRequestTypesTool.php`
- `ShowTypeSelectorTool.php`
- `GetDraftStatusTool.php`
- `UpdateTitleTool.php`
- `UpdateDescriptionTool.php`
- `UpdateFormFieldTool.php`
- `UpdatePriorityTool.php`
- `ShowFieldInputTool.php`
- `SubmitServiceRequestTool.php`
- `SaveClarifyingQuestionTool.php`
- `SubmitAiResolutionTool.php`
- `RecordResolutionResponseTool.php`
- `FinalizeServiceRequestTool.php`

### Jobs
- `app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`
  - Update `buildTools()` to instantiate new tools
  - Add `processInternalContent()` method for type_selection and field_response handling
  - Pass thread to all tools that need it

### Validators
- `app-modules/ai/src/Validators/InternalContentValidator.php`
  - Update validation for new internal_content types

---

## Implementation Order

### Phase 1: Database & Model Foundation
1. Create migration: add `is_draft`, `portal_assistant_thread_id`, `workflow_phase`, `clarifying_questions`, `ai_resolution` columns to service_requests
2. Update `ServiceRequest` model: add global scope for excluding drafts, add new column casts, add relationships
3. Update `PortalAssistantThread` model: add `draft()` relationship
4. Run migration

### Phase 2: Core Draft Tools
5. Create `FetchServiceRequestTypesTool` - creates draft, returns types
6. Create `ShowTypeSelectorTool` - emits UI action
7. Create `GetDraftStatusTool` - returns comprehensive draft state including form structure
8. Update `SendMessage` job to process `type_selection` internal_content

### Phase 3: Data Collection Tools
9. Create `UpdateTitleTool`
10. Create `UpdateDescriptionTool`
11. Create `UpdatePriorityTool`
12. Create `UpdateFormFieldTool`
13. Create `ShowFieldInputTool`
14. Update `SendMessage` job to process `field_response` internal_content

### Phase 4: Submission & Enrichment Tools
15. Create `SubmitServiceRequestTool` - validates, changes phase
16. Create `SaveClarifyingQuestionTool`
17. Create `SubmitAiResolutionTool`
18. Create `RecordResolutionResponseTool`
19. Create `FinalizeServiceRequestTool`

### Phase 5: Cleanup
20. Delete old tools
21. Update `buildTools()` in SendMessage job

---

## Key Behaviors to Verify

- Draft is created when user expresses intent to submit a service request
- Type selector shows AI suggestion highlighted but user can choose any type
- Title and description are collected before requesting form structure
- Form fields returned only for current type (previous type's fields stored but not shown)
- Text fields collected conversationally, complex fields via UI widgets
- Widget hides chat input but shows cancel option
- Field values from UI go directly to draft, not back through AI
- Validation errors list specific missing fields
- Exactly 3 clarifying questions are collected
- Resolution only shown to user if confidence meets threshold
- Service request is always created/saved regardless of resolution outcome
- Draft persists across page refreshes until finalized
