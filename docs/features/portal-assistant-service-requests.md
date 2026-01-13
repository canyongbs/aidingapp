# Portal Assistant Service Request Submission - Technical Specification

## Overview

The Portal Assistant Service Request feature enables authenticated portal users to submit service requests through an AI-powered conversational interface. The system guides users through information collection, asks clarifying questions, and optionally attempts automated resolution before routing to appropriate team members.

## Architecture

### Core Components

**SendMessage Job** (`app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`)

- Orchestrates AI streaming responses
- Controls tool availability based on draft stage
- Manages progressive disclosure of data collection tools

**Portal Assistant Tools** (`app-modules/ai/src/Tools/PortalAssistant/`)

- 12 specialized tools for type selection, data collection, enrichment, and submission
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

**Note:** Type and priority selection happen BEFORE the draft is created. The user interacts with the `show_type_selector` widget, selects a type and priority, which creates the draft with the priority already set. Once the draft exists, it's always in `data_collection` stage or beyond.

**Data Collection Order:** fields (if any) → description → title

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

- `fetch_service_request_types`: Always available when no active draft
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
- `update_description`, `request_file_attachments`: After required form fields filled (or immediately if no custom fields)
- `update_title`: After description provided

**Phase Transition:** When all required fields are filled, `get_draft_status` detects the stage is now `clarifying_questions` (derived from state) and instructs AI to begin asking questions.

### Clarifying Questions Stage

**Tool Availability:**

- `save_clarifying_question`: Always available, accepts exactly 3 question/answer pairs

**Requirements:**

- Exactly 3 questions must be asked and saved
- Questions must be specific to user's situation, drawing from collected data
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

**fetch_service_request_types**

- Returns hierarchical tree of available service request types
- Used by AI to understand available types before showing selector
- Only available when no active draft exists

**show_type_selector**

- Emits `PortalAssistantActionRequest` event with type tree and optional suggestion
- Frontend displays widget, hides main chat input
- User selection processed via dedicated controller
- Only available when no active draft exists

**cancel_service_request**

- Clears `current_service_request_draft_id` on the thread, deactivating the current draft
- Only available when an active draft exists
- Draft is retained (not deleted) so progress can be restored if same type selected again
- After calling, type selection tools become available again
- Use when user wants to start over or select a different request type

### Data Collection Tools

**get_draft_status**

- Returns complete draft state including form structure, filled fields, missing requirements
- Derives current phase from draft state
- Provides phase-specific instructions
- Detects when all required data filled and instructs transition to clarifying questions

**update_title**, **update_description**

- Saves conversational text input to draft
- Description stored in `close_details` field

**request_file_attachments**

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

### Enrichment Tools

**save_clarifying_question**

- Creates two updates immediately:
    - Question update with `update_type = 'clarifying_question'` (created_by = ServiceRequest)
    - Answer update with `update_type = 'clarifying_answer'` (created_by = Contact)
- After 3rd question saved, phase becomes `resolution` (derived from update count)
- Returns remaining count and next instruction

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
- Returns request number

**If user rejected resolution:**

- Sets status to "New" (open for human review)
- Generates `service_request_number` and sets `is_draft = false`
- Assigns to team member via type's assignment strategy
- Returns request number

## Type Switching Behavior

Users may change service request type by first canceling the current draft:

**Canceling Current Draft:**

- User asks to start over or change request type
- AI calls `cancel_service_request` tool
- Thread's `current_service_request_draft_id` set to null
- Draft retained (not deleted) for potential restoration
- Type selection tools (`fetch_service_request_types`, `show_type_selector`) become available
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
3. Uploading/uploaded files display as badges above input with progress indicator and X remove button
4. On message send, file URLs attached to message payload (`file_urls` array)
5. Backend copies files from temp S3 path to service request's `uploads` media collection
6. Attachment UI disabled and badges cleared after send

**Implementation:**

- `portals/knowledge-management/src/Components/Assistant/ChatInput.vue`
- `portals/knowledge-management/src/Components/Assistant/AssistantWidget.vue`
- `portals/knowledge-management/src/Composables/assistant/useAssistantChat.js`

## AI Instructions

The AI receives general guidance in the system context. Detailed tool-specific instructions belong in tool descriptions, not the system context.

**System Context Should Contain:**

- General workflow overview (type selection → data collection → clarifying questions → resolution)
- High-level behavioral guidelines (ask one question at a time, be conversational)
- Information about the current draft state (injected dynamically)

**Tool Descriptions Should Contain:**

- Detailed instructions for when/how to use each tool
- Parameter requirements and validation rules
- What the tool returns and what to do next
- Stage-specific constraints and transitions

**Minimizing Tool Call Chains:**

Action tools (`update_form_field`, `update_description`, `update_title`, `save_clarifying_question`, etc.) return the full draft status in their response. The AI does not need to call `get_draft_status` after each action—the next instruction is included in the tool response.

`get_draft_status` is primarily needed:
- At conversation start to understand current state (if resuming a draft)
- As a fallback if the AI loses track of state

**Developer Messages Include Draft Status:**

When controllers handle widget interactions (type selection, complex field inputs), the Developer message includes the full draft status. The AI does not need to call `get_draft_status` after these interactions.

**Ideal Tool Call Flow:**

1. User initiates service request → AI calls `fetch_service_request_types` then `show_type_selector`
2. User selects type via widget → Developer message includes draft status and next instruction
3. AI collects each field → Tool response includes draft status and next instruction
4. User submits complex field via widget → Developer message includes draft status and next instruction
5. After all fields collected → Response indicates transition to clarifying questions
6. AI asks/saves each question → Tool response includes remaining count and next instruction
7. After 3rd question → Tool response indicates resolution phase (or auto-submits if disabled)
8. AI calls `check_ai_resolution_validity` → Response indicates whether to present or auto-submit
9. If presented, AI calls `record_resolution_response` → Response includes final request number

## Implementation Notes

**File Attachment Handling:**
Files upload to temporary S3 via presigned URLs. On message send, `file_urls` array is included in the payload. Backend copies files to the service request's `uploads` media collection and cleans up temporary files.

---

## Detailed Conversation Log

This section documents a complete conversation from the assistant's perspective, showing exactly what tools are available, their descriptions, tool responses, and whether the assistant has sufficient context to proceed.

### Turn 1: User Initiates Request

**User Message:** "I can't log into the student portal"

**Available Tools:**

| Tool | Description |
|------|-------------|
| `fetch_service_request_types` | STEP 1: Retrieves the complete list of available service request types. Call this ONLY when: (1) User first expresses intent to submit a NEW service request, or (2) User explicitly wants to CHANGE to a different type. DO NOT call if already working on a draft unless user explicitly requests to change types. Returns a types_tree to analyze before calling show_type_selector. |
| `show_type_selector` | STEP 2: Displays a UI widget to select a service request type. Call this AFTER fetch_service_request_types ONLY when starting a NEW request or when user explicitly wants to CHANGE types. DO NOT call if already collecting data for an existing draft. If you identified a strong match in the types_tree, you MUST pass that exact type_id UUID as suggested_type_id parameter. If no match, omit the parameter entirely. |
| `get_draft_status` | Returns the current draft state with form fields, missing requirements, and next_instruction. Call at conversation start if resuming a draft, or as a fallback if you lose track of state. Action tools already include this info in their responses. |

**Assistant Decision:** User wants to submit a service request. Call `fetch_service_request_types`.

**Tool Call:** `fetch_service_request_types()`

**Tool Response:**
```json
{
  "types_tree": [
    {
      "name": "IT Support",
      "types": [
        {"type_id": "a1b2c3d4-...", "name": "Password Reset"},
        {"type_id": "e5f6g7h8-...", "name": "Software Installation"}
      ]
    }
  ],
  "has_draft": false,
  "next_instruction": "Analyze types_tree for a match. If found, call show_type_selector(suggested_type_id=\"<uuid>\"). If no match, call show_type_selector() with no parameters."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` says to analyze types and call `show_type_selector`. User mentioned "log into student portal" which matches "Password Reset".

**Tool Call:** `show_type_selector(suggested_type_id="a1b2c3d4-...")`

**Tool Response:** `"Widget displayed to user."`

**Assistant Response:** "I think this might be a Password Reset request. Please confirm or select a different type."

---

### Turn 2: User Selects Type via Widget

**User Action:** Clicks "Password Reset" in widget, selects "High" priority, confirms

**Note:** This is NOT a regular user message. The frontend sends the selection to `SelectServiceRequestTypeController`, which creates the draft and dispatches `SendMessage` with a Developer message.

**Developer Message (internal_content):**
```json
{
  "event": "type_selected",
  "has_draft": true,
  "draft_stage": "data_collection",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": null,
  "description": null,
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": null, "filled": false}
  ],
  "missing_required": ["f1f2f3f4-...", "description", "title"],
  "optional_fields": [],
  "clarifying_questions": {"completed": 0, "required": 3},
  "next_instruction": "Next: Ask about \"Student ID\"."
}
```

**Available Tools:**

| Tool | Description |
|------|-------------|
| `get_draft_status` | Returns the current draft state with form fields, missing requirements, and next_instruction. Call at conversation start if resuming a draft, or as a fallback if you lose track of state. Action tools already include this info in their responses. |
| `cancel_service_request` | Cancels the current service request draft, allowing the user to start over or select a different request type. Use when the user wants to abandon their current request, change to a different type, or start fresh. The draft is preserved and can be restored if the user selects the same type again. |
| `update_form_field` | Saves a text-based form field (text, textarea, number, email). For complex fields (select, date, signature), use show_field_input instead. Ask user for this field, wait for response, then save. |
| `show_field_input` | Displays a UI widget for complex form fields like selects, radio buttons, dates, phone numbers, addresses, or signatures. Do NOT use this for simple text fields. IMPORTANT: In the SAME response where you call this tool, also ask the user a natural question about what information you need. Do both together - ask the question AND call this tool in a single response. |
| `update_description` | Saves the issue description. Ask user to describe their issue, wait for response, then save. If form fields already captured details, you may save a brief summary like "See form details". |
| `request_file_attachments` | Enables optional file attachment capability for the user. Call this BEFORE asking for the description to allow users to attach supporting files (screenshots, documents, etc.) with their message. The attachment UI will be enabled until the user sends their next message. |
| `update_title` | Saves the service request title. Suggest a concise title based on collected info, ask user to confirm or modify, then save. Example: "How about: [title]?" |
| `save_clarifying_question` | Saves a clarifying question and answer. Call immediately after user answers. Exactly 3 questions required. Ask SPECIFIC questions based on collected data (e.g., "You mentioned X - did Y happen?"), not generic ones (e.g., "What OS?"). Do NOT provide solutions during this phase. |

**Does assistant know what to do?** ✅ Yes - Developer message includes full draft status with `next_instruction: "Next: Ask about \"Student ID\"."` The field type is "text" so assistant should ask and wait for response.

**Assistant Response:** "What is your Student ID number?"

---

### Turn 3: User Provides Student ID

**User Message:** "A00123456"

**Available Tools:** Same as Turn 2

**Assistant Decision:** User provided Student ID. Call `update_form_field` with the value.

**Tool Call:** `update_form_field(field_id="f1f2f3f4-...", value="A00123456")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "data_collection",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": null,
  "description": null,
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": ["description", "title"],
  "optional_fields": [],
  "clarifying_questions": {"completed": 0, "required": 3},
  "next_instruction": "Next: Ask for description of their issue."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` says to ask for description. Tool description for `request_file_attachments` says to call it BEFORE asking for description.

**Tool Call:** `request_file_attachments()`

**Tool Response:**
```json
{
  "success": true,
  "next_instruction": "File attachments enabled. Mention user can attach relevant files if helpful."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` says to mention file attachments. Previous tool response said to ask for description.

**Assistant Response:** "Please describe what's happening when you try to log in. You can attach screenshots if that helps."

---

### Turn 4: User Provides Description

**User Message:** "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times."

**Available Tools:** Same as Turn 2

**Assistant Decision:** User provided description. Call `update_description`.

**Tool Call:** `update_description(description="When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "data_collection",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": null,
  "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": ["title"],
  "optional_fields": [],
  "clarifying_questions": {"completed": 0, "required": 3},
  "next_instruction": "Next: Suggest a title based on collected information."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` says to suggest a title. Tool description for `update_title` says to suggest and ask user to confirm.

**Assistant Response:** "I need a brief title for your request. How about: 'Cannot log into student portal - invalid credentials error'?"

---

### Turn 5: User Confirms Title

**User Message:** "Yes that's fine"

**Available Tools:** Same as Turn 2

**Assistant Decision:** User confirmed title. Call `update_title`.

**Tool Call:** `update_title(title="Cannot log into student portal - invalid credentials error")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "clarifying_questions",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": "Cannot log into student portal - invalid credentials error",
  "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": [],
  "optional_fields": [],
  "clarifying_questions": {"completed": 0, "required": 3},
  "next_instruction": "Ask clarifying question 1 of 3. After user answers, call save_clarifying_question with both your question and their answer."
}
```

**Does assistant know what to do?** ✅ Yes - `draft_stage` changed to `clarifying_questions`. `next_instruction` says to ask question 1 of 3. Tool description says to ask SPECIFIC questions based on collected data.

**Assistant Response:** "You mentioned you've tried 5 times today. When did this problem start - was it working fine before today?"

---

### Turn 6: User Answers Question 1

**User Message:** "It was working yesterday, the problem started this morning"

**Available Tools:** Same as Turn 2

**Assistant Decision:** User answered. Call `save_clarifying_question` with question and answer.

**Tool Call:** `save_clarifying_question(question="You mentioned you've tried 5 times today. When did this problem start - was it working fine before today?", answer="It was working yesterday, the problem started this morning")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "clarifying_questions",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": "Cannot log into student portal - invalid credentials error",
  "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": [],
  "optional_fields": [],
  "clarifying_questions": {"completed": 1, "required": 3},
  "next_instruction": "Ask clarifying question 2 of 3. After user answers, call save_clarifying_question with both your question and their answer."
}
```

**Does assistant know what to do?** ✅ Yes - `clarifying_questions.completed` is 1. `next_instruction` says to ask question 2 of 3.

**Assistant Response:** "Are you typing your password manually or using a saved/autofill password?"

---

### Turn 7: User Answers Question 2

**User Message:** "I'm using the saved password from Chrome"

**Available Tools:** Same as Turn 2

**Tool Call:** `save_clarifying_question(question="Are you typing your password manually or using a saved/autofill password?", answer="I'm using the saved password from Chrome")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "clarifying_questions",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": "Cannot log into student portal - invalid credentials error",
  "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": [],
  "optional_fields": [],
  "clarifying_questions": {"completed": 2, "required": 3},
  "next_instruction": "Ask clarifying question 3 of 3. After user answers, call save_clarifying_question with both your question and their answer."
}
```

**Does assistant know what to do?** ✅ Yes - `clarifying_questions.completed` is 2. `next_instruction` says to ask question 3 of 3.

**Assistant Response:** "Have you tried logging in from a different browser or device to see if the issue persists?"

---

### Turn 8: User Answers Question 3

**User Message:** "No I haven't tried that"

**Available Tools:** Same as Turn 2, plus:

| Tool | Description |
|------|-------------|
| `check_ai_resolution_validity` | Checks if an AI resolution meets the confidence threshold. Call this after clarifying questions when you have a potential solution. DO NOT show the resolution to the user until this tool returns meets_threshold: true. |

**Tool Call:** `save_clarifying_question(question="Have you tried logging in from a different browser or device to see if the issue persists?", answer="No I haven't tried that")`

**Tool Response:**
```json
{
  "success": true,
  "has_draft": true,
  "draft_stage": "resolution",
  "type_id": "a1b2c3d4-...",
  "type_name": "Password Reset",
  "priority_id": "p1p2p3p4-...",
  "priority_name": "High",
  "title": "Cannot log into student portal - invalid credentials error",
  "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
  "form_fields": [
    {"field_id": "f1f2f3f4-...", "label": "Student ID", "type": "text", "required": true, "value": "A00123456", "filled": true}
  ],
  "missing_required": [],
  "optional_fields": [],
  "clarifying_questions": {"completed": 3, "required": 3},
  "next_instruction": "Call check_ai_resolution_validity with your confidence score and proposed answer."
}
```

**Does assistant know what to do?** ✅ Yes - `draft_stage` changed to `resolution`. `clarifying_questions.completed` is 3. `next_instruction` explicitly says to call `check_ai_resolution_validity`.

**Tool Call:** `check_ai_resolution_validity(confidence_score=82, proposed_answer="Based on your situation, the issue is likely with Chrome's saved password. Try these steps: 1) Go to Chrome settings > Passwords, 2) Find and delete the saved password for the student portal, 3) Go back to the login page and type your password manually. If this doesn't work, try the 'Forgot Password' link to reset it.")`

**Tool Response:**
```json
{
  "meets_threshold": true,
  "next_instruction": "Present resolution to user and ask if it solved their problem. Then call record_resolution_response with their answer."
}
```

**Does assistant know what to do?** ✅ Yes - `meets_threshold` is true. `next_instruction` says to present resolution and ask if it solved the problem.

**Assistant Response:** "Based on your situation, the issue is likely with Chrome's saved password. Try these steps:

1. Go to Chrome settings > Passwords
2. Find and delete the saved password for the student portal
3. Go back to the login page and type your password manually

If this doesn't work, try the 'Forgot Password' link to reset it.

Did this solve your problem?"

---

### Turn 9: User Rejects Resolution

**User Message:** "No, I tried that and it still doesn't work"

**Available Tools:** Same as Turn 8, plus:

| Tool | Description |
|------|-------------|
| `record_resolution_response` | Records the user's response to your resolution attempt. Call this after presenting a resolution and getting explicit yes/no feedback. |

**Assistant Decision:** User said resolution didn't work. Call `record_resolution_response` with accepted=false.

**Tool Call:** `record_resolution_response(accepted=false)`

**Tool Response:**
```json
{
  "success": true,
  "request_number": "SR-2026-00542",
  "resolution_accepted": false,
  "next_instruction": "Submitted for human review. Tell user request number SR-2026-00542 and that a team member will follow up."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` explicitly tells the assistant what to say.

**Assistant Response:** "I've submitted your request for human review. Your request number is SR-2026-00542. A team member will follow up with you soon to help resolve the login issue."

---

### Summary: Information Flow

At each turn, the assistant knows what to do because:

1. **Tool descriptions** explain when and how to use each tool
2. **Tool responses** include `next_instruction` with explicit guidance
3. **Draft status** is included in responses, showing current state and what's missing
4. **Stage transitions** are automatic and visible in the response (`draft_stage` changes)

The assistant never needs to call `get_draft_status` separately because all action tools return the full draft status. The only exception is when resuming a conversation with an existing draft, where `get_draft_status` provides the initial context.
