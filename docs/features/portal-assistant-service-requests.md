# Portal Assistant Service Request Submission

## Overview

The Portal Assistant Service Request feature enables authenticated portal users to submit service requests through an AI-powered conversational interface. The system guides users through information collection, asks clarifying questions, and optionally attempts automated resolution before routing to appropriate team members.

## Architecture

### Core Components

**SendMessage Job** (`app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`)

- Orchestrates AI streaming responses
- Controls tool availability based on draft stage
- Manages progressive disclosure of data collection tools

**Portal Assistant Tools** (`app-modules/ai/src/Tools/PortalAssistant/`)

- Specialized tools for type selection, data collection, enrichment, and submission
- Stage-gated availability prevents workflow bypass
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

## Draft Stages

Service request drafts progress through sequential stages. The stage is **derived from the current state** of the draft:

1. **data_collection** - Draft exists, collecting required fields (form fields, description, title)
2. **clarifying_questions** - All required fields filled, collecting 3 clarifying question/answer pairs
3. **resolution** - 3 clarifying questions completed, evaluating and presenting AI resolution (if enabled)

Type and priority selection happen BEFORE the draft is created. The user interacts with the `show_type_selector` widget, selects a type and priority, which creates the draft with the priority already set. Once the draft exists, it's always in `data_collection` stage or beyond.

**Data Collection Order:** fields (if any, ordered by step then position within step) → description → title

Stage determination logic:

```php
if (hasMissingRequiredFields($draft)) return 'data_collection';
if (clarifyingQuestionsCount($draft) < 3) return 'clarifying_questions';
return 'resolution';
```

When submitted (finalized), `is_draft = false` and `service_request_number` is generated. Status set to "Closed" if resolution accepted, "New" if rejected or resolution disabled/below threshold.

## Progressive Tool Disclosure

Tools unlock sequentially as prerequisites are met, preventing users from being overwhelmed by simultaneous choices while maintaining flexibility to edit previous responses.

### Type Selection (No Active Draft)

**Tool Availability:**

- `get_service_request_types_for_suggestion`: Always available when no active draft
- `show_type_selector`: Always available when no active draft
- `get_draft_status`: Always available (returns instruction to select type if no draft)

Once a type and priority are selected, the draft is created and these tools become unavailable. The user must cancel the current draft to access type selection again.

### Active Draft (Any Stage)

**Tool Availability:**

- `cancel_service_request`: Available whenever an active draft exists
- `get_draft_status`: Always available

The `cancel_service_request` tool allows users to abandon the current service request and start over with type selection. It sets `current_service_request_draft_id` to null on the thread, making type selection tools available again. The draft itself is retained, so if the user selects the same type again, their previous progress is restored.

### Data Collection Stage

**Collection Order:** Custom form fields (if present) → Description → Title

**Tool Availability:**

- `update_form_field`, `show_field_input`: If service request type defines custom fields
- `update_description`, `enable_file_attachments`: After required form fields filled (or immediately if no custom fields)
- `update_title`: After description provided

**Phase Transition:** When all required fields are filled, `get_draft_status` detects the stage is now `clarifying_questions` (derived from state) and instructs AI to begin asking questions.

### Optional Field Handling

Optional fields are surfaced contextually to the AI without blocking progress:

**During Custom Form Field Collection:**

When the AI is instructed to collect the next required field, any optional fields that were "skipped over" (between the last filled field and the next required field) are mentioned in the instruction. This allows the AI to:

- Proactively ask about them if they seem relevant based on the conversation context
- Skip them if they don't seem relevant
- Never block progress - optional fields are offered, not required

Example instruction:

```
"Ask for their department. You skipped these optional fields: Preferred Name, Secondary Email - ask about them if they seem relevant based on the conversation."
```

**Before Description (End of Custom Form Fields):**

When transitioning to the description field (meaning all required custom form fields are complete), ALL remaining unfilled optional fields are listed. This gives the AI one final opportunity to collect any optional information before moving to description/title.

Example instruction:

```
"Call enable_file_attachments() first. Then ask: 'Is there anything else you'd like to add...' Before moving on, these optional fields are still available: Additional Notes, Preferred Contact Method - ask about them if they seem relevant based on the conversation."
```

**Multi-Step Forms:**

Fields are collected in step order (by `ServiceRequestFormStep.sort`). The optional field logic respects this ordering - skipped optional fields are those between the last filled field and the next required field in the step-ordered sequence.

### Clarifying Questions Stage

**Tool Availability:**

- `save_clarifying_question_answer`: Always available, accepts exactly 3 question/answer pairs

**Requirements:**

- Exactly 3 questions must be asked and saved
- Questions gather additional information needed to understand the user's situation
- Questions are NOT for re-collecting form data already provided
- Questions focus on context, urgency, troubleshooting history, or other relevant details
- AI receives all previously collected data (title, description, filled form fields) to avoid duplicate questions
- Each question/answer saved immediately after user responds

**Phase Transition:**

- After 3rd question saved, phase becomes `resolution` (derived from update count)
- If AI resolution disabled, service request automatically submitted for human review after 3rd question

### Resolution Phase

**Tool Availability (if AI resolution enabled):**

- `check_ai_resolution_validity`: Available immediately after 3 questions saved
- `record_resolution_response`: Available only after confidence meets threshold and resolution presented

**Tool Availability (if AI resolution disabled):**

- Phase skipped, proceeds directly to submission

**Process (AI Resolution Enabled):**

1. AI calls `check_ai_resolution_validity` with confidence score (0-100) and proposed resolution
2. Tool compares score against configured threshold and creates resolution update

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
- Returns request number to AI

**If user says resolution was not helpful:**

- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = false`
- Sets status to "New" (open for human review)
- Generates `service_request_number` and sets `is_draft = false`
- Assigns to team member
- Returns request number to AI

**Process (AI Resolution Disabled):**

- After 3rd question saved, service request automatically submitted for human review
- Sets status to "New", generates request number
- Assigns to team member

## Tool Reference

### Type Selection Tools

**get_service_request_types_for_suggestion**

- Returns hierarchical tree of available service request types
- Used by AI to understand available types before showing selector
- Only available when no active draft exists

**show_type_selector**

- Emits `PortalAssistantActionRequest` event with type tree and optional suggestion
- Frontend displays widget, hides main chat input
- User selection processed via dedicated controller
- Only available when no active draft exists
- Returns suggested_type_name and conversational prompt for the AI to use

**cancel_service_request**

- Clears `current_service_request_draft_id` on the thread, deactivating the current draft
- Only available when an active draft exists
- Draft is retained (not deleted) so progress can be restored if same type selected again
- After calling, type selection tools become available again
- Use when user wants to start over or select a different request type

### Data Collection Tools

**get_draft_status**

- Returns stage-appropriate draft state with conversational instructions in structured JSON format
- Derives current phase from draft state
- During `data_collection`: Returns `missing_required_fields` and `missing_optional_fields` arrays
- During `clarifying_questions`/`resolution`: Returns `questions_completed` counter, `title`, `description`, and `filled_form_fields` array
- `filled_form_fields` contains structured label/value pairs with special handling:
    - Signature fields: `[Signature provided]` instead of base64 data
    - Checkbox fields: `Yes`/`No` instead of boolean
    - All field values limited to 255 characters (truncated with `...`)
    - Title limited to 255 characters
    - Description provided in full (not truncated)
- AI receives this structured data to understand what information has already been collected
- Provides natural-language `next_instruction` guiding the AI on what to do next

**update_title**, **update_description**

- Saves conversational text input to draft
- Description stored in `close_details` field

**enable_file_attachments**

- Called when prompting user for description to enable optional file uploads
- Emits `PortalAssistantActionRequest` with `action_type = 'enable_file_attachments'`
- Frontend shows attachment button next to send button
- Files uploaded to temporary S3 directory via presigned URL, displayed as badges above input with upload progress and remove button
- File URLs included in next message payload; backend copies files from temp directory to service request's `uploads` media collection
- Attachment UI automatically disabled after message sent

**update_form_field**

- Saves simple text field responses to `ServiceRequestFormSubmission`
- For text-based custom fields only

**show_field_input**

- Displays widget for complex field types (select, date, file, etc.)
- Frontend hides chat input while widget active
- Response saved directly via controller, not returned to AI
- Returns structured JSON with field_label and next_instruction
- AI should call this tool AND ask a natural question in the same response

### Enrichment Tools

**save_clarifying_question_answer**

- Creates two updates immediately:
    - Question update with `update_type = 'clarifying_question'` (created_by = ServiceRequest)
    - Answer update with `update_type = 'clarifying_answer'` (created_by = Contact)
- After 3rd question saved, phase becomes `resolution` (derived from update count) if AI resolution enabled, otherwise submits immediately
- Returns remaining count and contextual guidance
- Tool description emphasizes questions are for gathering additional information, not form data re-collection

### Resolution Tools

**check_ai_resolution_validity**

- Accepts confidence score (0-100) and proposed resolution text
- Compares score against configured threshold
- Creates resolution update with `update_type = 'ai_resolution_proposed'`
- Stores confidence score in `ai_resolution_confidence_score` field

**If confidence below threshold:**

- Automatically submits service request without showing resolution to user
- Sets `is_ai_resolution_attempted = true`, `is_ai_resolution_successful = false`
- Assigns to team member, generates request number
- Sets `is_draft = false`, status to "New"
- Returns request number and success message

**If confidence meets threshold:**

- Instructs AI to present resolution to user and wait for response
- Does NOT submit yet - waits for user feedback

**record_resolution_response**

- Creates response update with `update_type = 'ai_resolution_response'` containing user's answer
- Sets `is_ai_resolution_attempted = true`
- Sets `is_ai_resolution_successful` based on user response

**If user accepted resolution:**

- Sets status to "Closed" (resolved without human intervention)
- Generates `service_request_number` and sets `is_draft = false`
- Does NOT assign to team (no human review needed)
- Returns request number with friendly message to convey

**If user rejected resolution:**

- Sets status to "New" (open for human review)
- Generates `service_request_number` and sets `is_draft = false`
- Assigns to team member via type's assignment strategy
- Returns request number with message about human follow-up

## Type Switching Behavior

Users may change service request type by first canceling the current draft:

**Canceling Current Draft:**

- User asks to start over or change request type
- AI calls `cancel_service_request` tool
- Thread's `current_service_request_draft_id` set to null
- Draft retained (not deleted) for potential restoration
- Type selection tools (`get_service_request_types_for_suggestion`, `show_type_selector`) become available
- Data collection tools become unavailable

**Selecting a Different Type:**

- New draft created for selected type
- `current_service_request_draft_id` updated to new draft
- Workflow starts at `data_collection` phase

**Re-selecting Previously Used Type:**

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

**File Attachments:**

When `enable_file_attachments` action received:

1. Attachment button appears next to send button
2. User selects files; each file uploads to temporary S3 directory via presigned URL
3. Uploading/uploaded files display as badges above input with progress indicator and remove button
4. On message send, file URLs attached to message payload (`file_urls` array)
5. Backend copies files from temp S3 path to service request's `uploads` media collection
6. Attachment UI disabled and badges cleared after send

**Key Frontend Files:**

- `portals/knowledge-management/src/Components/Assistant/ChatInput.vue`
- `portals/knowledge-management/src/Components/Assistant/AssistantWidget.vue`
- `portals/knowledge-management/src/Composables/assistant/useAssistantChat.js`

## AI Instruction Design

The AI receives general guidance in the system context. Detailed tool-specific instructions belong in tool descriptions, not the system context.

**System Context Contains:**

- General workflow overview (type selection → data collection → clarifying questions → resolution)
- High-level behavioral guidelines (ask one question at a time, be conversational)
- Information about the current draft state (injected dynamically)

**Tool Descriptions Contain:**

- Detailed instructions for when/how to use each tool
- Parameter requirements and validation rules
- What the tool returns and what to do next
- Stage-specific constraints and transitions

**Minimizing Tool Call Chains:**

Action tools (`update_form_field`, `update_description`, `update_title`, `save_clarifying_question_answer`, etc.) return the full draft status in their response. The AI does not need to call `get_draft_status` after each action - the next instruction is included in the tool response.

`get_draft_status` is primarily needed:

- At conversation start to understand current state (if resuming a draft)
- As a fallback if the AI loses track of state

**Developer Messages Include Draft Status:**

When controllers handle widget interactions (type selection, complex field inputs), the Developer message includes the full draft status. The AI does not need to call `get_draft_status` after these interactions.

---

## Conversation Example

This section documents a complete conversation showing tool availability, tool responses, and the data flow through each stage.

### Turn 1: User Initiates Request

**User Message:** "I can't log into the student portal"

**Available Tools:**

| Tool | Description |
| ---- | ----------- |
| `get_service_request_types_for_suggestion` | Retrieves the complete list of available service request types. Returns a types_tree to analyze before calling show_type_selector. |
| `show_type_selector` | Displays a UI widget to select a service request type. If a strong match is identified in the types_tree, pass the type_id UUID as suggested_type_id parameter. |

**Tool Call:** `get_service_request_types_for_suggestion()`

**Tool Response:**

```json
{
    "types_tree": [
        {
            "name": "IT Support",
            "types": [
                { "type_id": "a1b2c3d4-...", "name": "Password Reset" },
                { "type_id": "e5f6g7h8-...", "name": "Software Installation" }
            ]
        }
    ],
    "next_instruction": "Analyze types_tree. Call show_type_selector(suggested_type_id=\"<uuid>\") if match found, otherwise show_type_selector() with no parameters."
}
```

**Tool Call:** `show_type_selector(suggested_type_id="a1b2c3d4-...")`

**Tool Response:**

```json
{
    "success": true,
    "suggested_type_name": "Password Reset",
    "next_instruction": "Tell user: \"Based on what you described, I think 'Password Reset' might be what you need. Please confirm or select a different type from the options above.\" Then wait for their selection."
}
```

**Assistant Response:** "Based on what you described, I think 'Password Reset' might be what you need. Please confirm or select a different type from the options above."

---

### Turn 2: User Selects Type via Widget

**User Action:** Clicks "Password Reset" in widget, selects "High" priority, confirms

The frontend sends the selection to `SelectServiceRequestTypeController`, which creates the draft and dispatches `SendMessage` with a Developer message.

**Developer Message (internal_content):**

```json
{
    "event": "type_selected",
    "draft_stage": "data_collection",
    "type_name": "Password Reset",
    "title": null,
    "description": null,
    "missing_required_fields": [{ "field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text" }],
    "missing_optional_fields": [{ "field_id": "x9y8z7w6-...", "label": "Additional Notes", "type": "textarea" }],
    "has_custom_form_fields": true,
    "next_instruction": "Ask naturally for their student id (e.g., \"What's your student id?\"). After they respond, call update_form_field(field_id=\"f1f2f3f4-...\", value=\"<their response>\")."
}
```

**Available Tools:** `get_draft_status`, `cancel_service_request`, `update_form_field`, `show_field_input`, `update_description`, `enable_file_attachments`, `update_title`, `save_clarifying_question_answer`

**Assistant Response:** "Great! What's your Student ID?"

---

### Turn 3: User Provides Student ID

**User Message:** "A00123456"

**Tool Call:** `update_form_field(field_id="f1f2f3f4-...", value="A00123456")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "data_collection",
    "type_name": "Password Reset",
    "title": null,
    "description": null,
    "missing_required_fields": [{ "field_id": "description", "label": "Description", "type": "description" }],
    "missing_optional_fields": [{ "field_id": "x9y8z7w6-...", "label": "Additional Notes", "type": "textarea" }],
    "has_custom_form_fields": true,
    "next_instruction": "Call enable_file_attachments() first. Then ask: \"Is there anything else you'd like to add about this request? Feel free to attach any files if helpful.\" Before moving on, these optional fields are still available: Additional Notes - ask about them if they seem relevant based on the conversation."
}
```

**Tool Call:** `enable_file_attachments()`

**Tool Response:**

```json
{
    "success": true,
    "next_instruction": "File attachments enabled. Now ask: \"Can you describe what's happening? Feel free to attach any screenshots or files if that helps.\" After they respond, call update_description(description=\"<their response>\")."
}
```

**Assistant Response:** "Is there anything else you'd like to add about this request? Feel free to attach any screenshots if that helps."

---

### Turn 4: User Provides Description

**User Message:** "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times."

**Tool Call:** `update_description(description="When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "data_collection",
    "type_name": "Password Reset",
    "title": null,
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "missing_required_fields": [{ "field_id": "title", "label": "Title", "type": "title" }],
    "missing_optional_fields": [{ "field_id": "x9y8z7w6-...", "label": "Additional Notes", "type": "textarea" }],
    "has_custom_form_fields": true,
    "next_instruction": "Based on what they've told you, suggest a short title. Say something like: \"I'll title this '[your suggested title]' - does that work?\" After they confirm (or suggest changes), call update_title(title=\"<final title>\")."
}
```

**Assistant Response:** "I'll title this 'Cannot log into student portal - invalid credentials error' - does that work?"

---

### Turn 5: User Confirms Title

**User Message:** "Yes that's fine"

**Tool Call:** `update_title(title="Cannot log into student portal - invalid credentials error")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "clarifying_questions",
    "type_name": "Password Reset",
    "title": "Cannot log into student portal - invalid credentials error",
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "filled_form_fields": [{ "label": "Student ID", "value": "A00123456" }],
    "questions_completed": 0,
    "next_instruction": "Question 1 of 3 (2 remaining). Ask clarifying questions that gather ADDITIONAL information - NOT to re-collect form data already provided. Focus on context, urgency, troubleshooting history, or other relevant details. After they answer, call save_clarifying_question_answer(question=\"<your question>\", answer=\"<their answer>\")."
}
```

**Assistant Response:** "When did you first notice this problem - was it working fine before today?"

---

### Turn 6: User Answers Question 1

**User Message:** "It was working yesterday, the problem started this morning"

**Tool Call:** `save_clarifying_question_answer(question="When did you first notice this problem - was it working fine before today?", answer="It was working yesterday, the problem started this morning")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "clarifying_questions",
    "questions_completed": 1,
    "next_instruction": "Question 2 of 3 (1 remaining). Continue asking clarifying questions..."
}
```

**Assistant Response:** "Are you typing your password manually or using a saved/autofill password from your browser?"

---

### Turn 7: User Answers Question 2

**User Message:** "I'm using the saved password from Chrome"

**Tool Call:** `save_clarifying_question_answer(question="Are you typing your password manually or using a saved/autofill password from your browser?", answer="I'm using the saved password from Chrome")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "clarifying_questions",
    "questions_completed": 2,
    "next_instruction": "Question 3 of 3 (0 remaining). Continue asking clarifying questions..."
}
```

**Assistant Response:** "Have you tried logging in from a different browser or device to see if the issue persists?"

---

### Turn 8: User Answers Question 3

**User Message:** "No I haven't tried that"

**Tool Call:** `save_clarifying_question_answer(question="Have you tried logging in from a different browser or device to see if the issue persists?", answer="No I haven't tried that")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "resolution",
    "type_name": "Password Reset",
    "title": "Cannot log into student portal - invalid credentials error",
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "filled_form_fields": [{ "label": "Student ID", "value": "A00123456" }],
    "questions_completed": 3,
    "next_instruction": "Based on everything the user told you, formulate a helpful resolution. Call check_ai_resolution_validity(confidence_score=<0-100>, proposed_answer=\"<your detailed resolution>\"). The tool will tell you whether to present it or submit for review."
}
```

**Tool Call:** `check_ai_resolution_validity(confidence_score=82, proposed_answer="Based on your situation, the issue is likely with Chrome's saved password. Try these steps: 1) Go to Chrome settings > Passwords, 2) Find and delete the saved password for the student portal, 3) Go back to the login page and type your password manually. If this doesn't work, try the 'Forgot Password' link to reset it.")`

**Tool Response:**

```json
{
    "meets_threshold": true,
    "next_instruction": "Present your resolution to the user in a helpful way. End by asking: \"Does this help resolve your issue?\" Based on their response, call record_resolution_response(accepted=true) if they say yes, or record_resolution_response(accepted=false) if they say no."
}
```

**Assistant Response:** "Based on what you've described, the issue is likely with Chrome's saved password being out of date. Here's what I'd suggest:

1. Go to Chrome settings > Passwords
2. Find and delete the saved password for the student portal
3. Go back to the login page and type your password manually

If that doesn't work, try the 'Forgot Password' link to reset it.

Does this help resolve your issue?"

---

### Turn 9: User Rejects Resolution

**User Message:** "No, I tried that and it still doesn't work"

**Tool Call:** `record_resolution_response(accepted=false)`

**Tool Response:**

```json
{
    "success": true,
    "request_number": "SR-2026-00542",
    "next_instruction": "Tell user: \"Your request number is SR-2026-00542. A team member will follow up to help resolve this.\""
}
```

**Assistant Response:** "I'm sorry that didn't work. I've submitted your request to our support team. Your request number is SR-2026-00542. A team member will follow up with you soon to help resolve this login issue."

---

## Key Design Principles

1. **Tool descriptions** explain WHEN to use each tool
2. **Tool responses** include `next_instruction` with conversational prompts and explicit tool call formats
3. **Draft status** is stage-appropriate - only relevant data for the current stage is included
4. **Context-aware prompts** adapt based on whether the form has custom fields
5. **Stage transitions** are automatic and visible in the response (`draft_stage` changes)
6. **Multi-step form ordering** - Fields are collected in step order (by `sort`), ensuring logical flow through form sections
7. **Action tools return draft status** - The assistant never needs to call `get_draft_status` separately after actions
