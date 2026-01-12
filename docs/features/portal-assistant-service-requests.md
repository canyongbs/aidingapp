# Portal Assistant Service Request Submission - Technical Specification

## Overview

The Portal Assistant Service Request feature enables authenticated portal users to submit service requests through an AI-powered conversational interface. The system guides users through information collection, asks clarifying questions, and optionally attempts automated resolution before routing to appropriate team members.

## Architecture

### Core Components

**SendMessage Job** (`app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`)
- Orchestrates AI streaming responses
- Controls tool availability based on workflow phase
- Manages progressive disclosure of data collection tools

**Portal Assistant Tools** (`app-modules/ai/src/Tools/PortalAssistant/`)
- 13 specialized tools for type selection, data collection, enrichment, and submission
- Phase-gated availability prevents workflow bypass
- No ID passing required - draft state managed automatically

**Service Request Drafts**
- Multiple drafts can exist per thread (one per service request type)
- Thread tracks active draft via `current_service_request_draft_id`
- Global scope excludes drafts from standard queries (`is_draft = false`)
- Drafts persist across sessions until finalized

**Widget Controllers**
- Dedicated HTTP controllers handle widget interactions (type/priority selection, form fields)
- Dispatch SendMessage with Developer messages providing state context
- Separate from tool calls to maintain clean AI conversation flow

### Database Schema

**service_requests table additions:**
```php
$table->boolean('is_draft')->default(false)->index();
$table->foreignUuid('portal_assistant_thread_id')->nullable()->index();
$table->string('workflow_phase')->nullable();
$table->json('clarifying_questions')->default('[]');
$table->json('ai_resolution')->nullable();
```

**Global Scope:**
```php
// Excludes drafts from standard queries
Builder::where('is_draft', false)
```

## Workflow Phases

Service requests progress through sequential phases:

1. **type_selection** - Draft created, awaiting type selection
2. **data_collection** - Type selected, collecting required information
3. **clarifying_questions** - Required data validated, collecting 3 contextual questions
4. **resolution** - Questions complete, evaluating and presenting AI resolution (if enabled)

When submitted (finalized), `is_draft = false`, `workflow_phase = null`, and `service_request_number` is generated. Status set to "Closed" if resolution accepted, "New" if rejected or resolution disabled/below threshold.

## Progressive Tool Disclosure

Tools unlock sequentially as prerequisites are met, preventing users from being overwhelmed by simultaneous choices while maintaining flexibility to edit previous responses.

### Data Collection Phase

**Collection Order:** Custom form fields (if present) → Description → Title → Priority

**Tool Availability:**
- `update_form_field`, `show_field_input`: If service request type defines custom fields
- `update_description`: After required form fields filled (or immediately if no custom fields)
- `update_title`: After description provided
- `show_priority_selector`: After title provided

**Phase Transition:** When all required fields are filled, `get_draft_status` automatically transitions draft to `clarifying_questions` phase and instructs AI to begin asking questions.

### Clarifying Questions Phase

**Tool Availability:**
- `save_clarifying_question`: Always available, accepts exactly 3 question/answer pairs

**Requirements:**
- Exactly 3 questions must be asked and saved
- Questions must be specific to user's situation, drawing from collected data
- Each question/answer saved immediately after user responds

**Phase Transition:** 
- After 3rd question saved, draft transitions to `resolution` phase (if AI resolution enabled)
- If AI resolution disabled, service request automatically submitted for human review after 3rd question

### Resolution Phase

**Tool Availability (if AI resolution enabled):**
- `check_ai_resolution_validity`: Available immediately after 3 questions saved
- `record_resolution_response`: Available only after confidence meets threshold and resolution presented

**Tool Availability (if AI resolution disabled):**
- Phase skipped, proceeds directly to submission

**Process (AI Resolution Enabled):**

1. AI calls `check_ai_resolution_validity` with confidence score (0-100) and proposed resolution
2. Tool compares score against configured threshold and stores `meets_threshold` boolean

**If confidence below threshold:**
- AI does NOT show resolution to user
- Tool automatically submits service request for human review
- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = false`
- Stores resolution attempt as internal update (not visible to user)
- Generates `service_request_number` and sets `is_draft = false`
- Returns request number to AI

**If confidence meets threshold:**
- Tool instructs AI to present resolution to user
- AI shows resolution and asks if it solved their problem
- User responds yes/no
- AI calls `record_resolution_response` with boolean

**If user says resolution was helpful:**
- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = true`
- Sets status to "Closed" (resolved without human intervention)
- Generates `service_request_number` and sets `is_draft = false`
- Creates update records for questions and resolution
- Returns request number to AI

**If user says resolution was not helpful:**
- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = false`
- Sets status to "New" (open for human review)
- Generates `service_request_number` and sets `is_draft = false`
- Creates update records for questions and resolution
- Assigns to team member
- Returns request number to AI

**Process (AI Resolution Disabled):**
- After 3rd question saved, service request automatically submitted for human review
- Sets status to "New", generates request number
- Creates update records for questions
- Assigns to team member

## Tool Reference

### Type Selection Tools

**fetch_service_request_types**
- Creates draft if none exists for thread
- Returns hierarchical tree of available service request types
- Sets `workflow_phase = 'type_selection'`

**show_type_selector**
- Emits `PortalAssistantActionRequest` event with type tree and optional suggestion
- Frontend displays widget, hides main chat input
- User selection processed via dedicated controller

### Data Collection Tools

**get_draft_status**
- Returns complete draft state including form structure, filled fields, missing requirements
- Provides phase-specific instructions
- Auto-transitions to `clarifying_questions` when all required data filled

**update_title**, **update_description**
- Saves conversational text input to draft
- Description stored in `close_details` field

**update_form_field**
- Saves simple text field responses to `ServiceRequestFormSubmission`
- For text-based custom fields only

**show_field_input**
- Displays widget for complex field types (select, date, file, etc.)
- Frontend hides chat input while widget active
- Response saved directly via controller, not returned to AI

**show_priority_selector**
- Displays priority selection widget after title provided
- User selection updates draft via dedicated controller

### Enrichment Tools

**save_clarifying_question**
- Appends question/answer pair to `clarifying_questions` JSON array
- Automatically transitions to `resolution` after 3rd question
- Returns remaining count and next instruction

### Resolution Tools

**check_ai_resolution_validity**
- Accepts confidence score (0-100) and proposed resolution text
- Compares score against configured threshold
- Stores result in `ai_resolution` JSON with `meets_threshold` boolean

**If confidence below threshold:**
- Automatically submits service request without showing resolution to user
- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = false`
- Creates update records for questions and resolution attempt (internal)
- Assigns to team member, generates request number
- Sets `is_draft = false`, status to "New"
- Returns request number and success message

**If confidence meets threshold:**
- Instructs AI to present resolution to user and wait for response
- Does NOT submit yet - waits for user feedback

**record_resolution_response**
- Records user's acceptance/rejection of presented resolution
- Sets `is_ai_resolution_attempted = true`
- Sets `is_ai_resolution_successful` based on user response

**If user accepted resolution:**
- Creates update records for questions and resolution
- Sets status to "Closed" (resolved without human intervention)
- Generates `service_request_number` and sets `is_draft = false`
- Does NOT assign to team (no human review needed)
- Returns request number

**If user rejected resolution:**
- Creates update records for questions and resolution attempt
- Sets status to "New" (open for human review)
- Generates `service_request_number` and sets `is_draft = false`
- Assigns to team member via type's assignment strategy
- Returns request number

## Type Switching Behavior

Users may change service request type during data collection:

**Switching to Different Type:**
- New draft created for selected type
- Old draft retained (not deleted)
- `current_service_request_draft_id` updated to new draft
- Workflow resets to `data_collection` phase

**Switching to Previously Selected Type:**
- Existing draft for that type restored
- `current_service_request_draft_id` updated to previous draft
- Previously collected data (title, description, fields) restored
- User continues from last state for that type

This design allows exploration of multiple request types without data loss while maintaining type-specific form field integrity.

## Frontend Widget Integration

Widgets temporarily replace chat input to focus user attention:

**Widget Display:**
1. Main chat input hidden
2. Widget rendered with available options
3. "Cancel" link displayed below widget

**Widget Interaction:**
- Selection: Sends `internal_content` message via dedicated controller
- Cancellation: Sends cancellation `internal_content`, restores chat input
- AI acknowledges and continues conversation

**Implementation:**
- `portals/knowledge-management/src/Components/Assistant/ChatInput.vue`
- `portals/knowledge-management/src/Components/Assistant/AssistantWidget.vue`
- `portals/knowledge-management/src/Composables/assistant/useAssistantChat.js`

## Internal Content Processing

Widget interactions and user selections processed via `internal_content`:

**type_selection:**
```json
{"type": "type_selection", "type_id": "uuid"}
```
Validates type, creates/updates draft and form submission, sets default priority, advances to `data_collection`.

**priority_selection:**
```json
{"type": "priority_selection", "priority_id": "uuid"}
```
Validates priority belongs to current type, updates draft.

**field_response:**
```json
{"type": "field_response", "field_id": "uuid", "value": {...}}
```
Validates field belongs to current type's form, saves via `ProcessServiceRequestSubmissionField` action.

**widget_cancelled:**
```json
{"type": "widget_cancelled", "widget_type": "...", "field_id": "..."}
```
No data change, restores chat input, AI acknowledges.

## AI Instructions

The AI receives phase-specific instructions emphasizing:

**Data Collection:**
- Ask ONE question at a time
- Call `get_draft_status` after each save to determine next step
- Never combine multiple questions
- Auto-transition occurs when all required fields filled

**Clarifying Questions:**
- Ask ONE question at a time
- Call `get_draft_status` after each save to determine next step
- Never combine multiple questions
- After 3rd question, phase automatically transitions to resolution (if enabled) or submits for human review

**Resolution:**
- Call `check_ai_resolution_validity` first with confidence and proposed answer
- If confidence below threshold: tool auto-submits request, AI informs user
- If confidence meets threshold: AI presents resolution and waits for user response
- Call `record_resolution_response` with user's yes/no answer
- Tool handles submission automatically based on user response

## Implementation Notes

**Automatic Submission:**
Both resolution tools (`check_ai_resolution_validity` and `record_resolution_response`) handle the final submission internally. There is no separate `submit_service_request` tool in the resolution phase. This ensures:
- Service requests are always submitted after resolution attempt
- Data integrity (resolution metrics always captured)
- Simplified AI interaction model

**Tool Naming:**
Previous iterations included separate validation and submission tools. Current design uses automatic phase transitions and resolution tools that handle submission internally, eliminating redundant steps.

**Validation:**
When all required fields are filled, `get_draft_status` automatically advances the workflow phase. This eliminates redundant validation calls and simplifies the AI interaction model.

**Field Order Flexibility:**
While the collection order is fields → description → title → priority, the system validates all required fields are present regardless of order. This accommodates edge cases where AI collects information out of sequence.
