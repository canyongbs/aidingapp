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
- AI should call this tool AND ask a natural question in the same response (e.g., "Which department does this relate to?" or "Please provide your signature.")

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

Action tools (`update_form_field`, `update_description`, `update_title`, `save_clarifying_question_answer`, etc.) return the full draft status in their response. The AI does not need to call `get_draft_status` after each action—the next instruction is included in the tool response.

`get_draft_status` is primarily needed:

- At conversation start to understand current state (if resuming a draft)
- As a fallback if the AI loses track of state

**Developer Messages Include Draft Status:**

When controllers handle widget interactions (type selection, complex field inputs), the Developer message includes the full draft status. The AI does not need to call `get_draft_status` after these interactions.

**Ideal Tool Call Flow:**

1. User initiates service request → AI calls `get_service_request_types_for_suggestion` then `show_type_selector`
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

| Tool                                       | Description                                                                                                                                                                                                                                                                                                                                                                                                                               |
| ------------------------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `get_service_request_types_for_suggestion` | STEP 1: Retrieves the complete list of available service request types. Call this ONLY when: (1) User first expresses intent to submit a NEW service request, or (2) User explicitly wants to CHANGE to a different type. DO NOT call if already working on a draft unless user explicitly requests to change types. Returns a types_tree to analyze before calling show_type_selector.                                                   |
| `show_type_selector`                       | STEP 2: Displays a UI widget to select a service request type. Call this AFTER get_service_request_types_for_suggestion ONLY when starting a NEW request or when user explicitly wants to CHANGE types. DO NOT call if already collecting data for an existing draft. If you identified a strong match in the types_tree, you MUST pass that exact type_id UUID as suggested_type_id parameter. If no match, omit the parameter entirely. |
| `get_draft_status`                         | Returns the current draft state with form fields, missing requirements, and next_instruction. Call at conversation start if resuming a draft, or as a fallback if you lose track of state. Action tools already include this info in their responses.                                                                                                                                                                                     |

**Assistant Decision:** User wants to submit a service request. Call `get_service_request_types_for_suggestion`.

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

**Does assistant know what to do?** ✅ Yes - `next_instruction` says to analyze types and call `show_type_selector`. User mentioned "log into student portal" which matches "Password Reset".

**Tool Call:** `show_type_selector(suggested_type_id="a1b2c3d4-...")`

**Tool Response:**

```json
{
    "success": true,
    "suggested_type_name": "Password Reset",
    "next_instruction": "Tell user: \"Based on what you described, I think 'Password Reset' might be what you need. Please confirm or select a different type from the options above.\" Then wait for their selection."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` provides the exact conversational prompt to use and tells them to wait.

**Assistant Response:** "Based on what you described, I think 'Password Reset' might be what you need. Please confirm or select a different type from the options above."

---

### Turn 2: User Selects Type via Widget

**User Action:** Clicks "Password Reset" in widget, selects "High" priority, confirms

**Note:** This is NOT a regular user message. The frontend sends the selection to `SelectServiceRequestTypeController`, which creates the draft and dispatches `SendMessage` with a Developer message.

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

**Available Tools:**

| Tool                              | Description                                                                                                                                                                                                                                                                                                                                                                    |
| --------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `get_draft_status`                | Returns the current draft state with form fields, missing requirements, and next_instruction. Call at conversation start if resuming a draft, or as a fallback if you lose track of state. Action tools already include this info in their responses.                                                                                                                          |
| `cancel_service_request`          | Cancels the current service request draft, allowing the user to start over or select a different request type. Use when the user wants to abandon their current request, change to a different type, or start fresh. The draft is preserved and can be restored if the user selects the same type again.                                                                       |
| `update_form_field`               | Saves a user's response to a text-based form field. Call this AFTER the user provides their answer to your question. For complex fields (select, date, signature), use show_field_input instead to display a widget.                                                                                                                                                           |
| `show_field_input`                | Displays a UI widget for complex form fields like selects, radio buttons, dates, phone numbers, addresses, or signatures. Do NOT use this for simple text fields. IMPORTANT: In the SAME response where you call this tool, also ask the user a natural question about what information you need. Do both together - ask the question AND call this tool in a single response. |
| `update_description`              | Saves the user's description of their issue. Call this AFTER the user describes their problem. Pass their full response as the description parameter.                                                                                                                                                                                                                          |
| `enable_file_attachments`         | Enables optional file attachment capability for the user. Call this BEFORE asking for the description to allow users to attach supporting files (screenshots, documents, etc.) with their message. The attachment UI will be enabled until the user sends their next message.                                                                                                  |
| `update_title`                    | Saves the service request title. Call this AFTER suggesting a title and the user confirms it. Keep titles concise (under 10 words) and descriptive of the core issue.                                                                                                                                                                                                          |
| `save_clarifying_question_answer` | Records your question and the user's answer. Call this IMMEDIATELY after the user answers your clarifying question - pass both what you asked AND their response. You must ask exactly 3 clarifying questions total. Focus on SPECIFIC details about their situation, not generic troubleshooting.                                                                             |

**Does assistant know what to do?** ✅ Yes - Developer message includes conversational guidance: `"Ask naturally for their student id (e.g., \"What's your student id?\")..."` with the exact field_id and tool call format.

**Assistant Response:** "Great! What's your Student ID?"

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
    "draft_stage": "data_collection",
    "type_name": "Password Reset",
    "title": null,
    "description": null,
    "missing_required_fields": [{ "field_id": "description", "label": "Description", "type": "description" }],
    "missing_optional_fields": [{ "field_id": "x9y8z7w6-...", "label": "Additional Notes", "type": "textarea" }],
    "has_custom_form_fields": true,
    "next_instruction": "Call enable_file_attachments() first. Then ask: \"Is there anything else you'd like to add about this request? Feel free to attach any files if helpful.\" IMMEDIATELY after they respond with ANY text, you MUST call update_description(description=\"<their response>\") before doing anything else. Before moving on, these optional fields are still available: Additional Notes - ask about them if they seem relevant based on the conversation."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` tells them to call `enable_file_attachments()` first. Since form fields were already collected, the prompt asks for "anything else to add" rather than describing the issue from scratch. All remaining optional fields (Additional Notes) are listed since we're transitioning to description, giving the AI a final opportunity to collect them if relevant.

**Tool Call:** `enable_file_attachments()`

**Tool Response:**

```json
{
    "success": true,
    "next_instruction": "File attachments enabled. Now ask: \"Can you describe what's happening? Feel free to attach any screenshots or files if that helps.\" After they respond, call update_description(description=\"<their response>\")."
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` provides the prompt to use.

**Assistant Response:** "Is there anything else you'd like to add about this request? Feel free to attach any screenshots if that helps."

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

**Does assistant know what to do?** ✅ Yes - `next_instruction` tells them exactly how to phrase the title suggestion. Optional fields were already offered before description, so they're not repeated here.

**Assistant Response:** "I'll title this 'Cannot log into student portal - invalid credentials error' - does that work?"

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
    "draft_stage": "clarifying_questions",
    "type_name": "Password Reset",
    "title": "Cannot log into student portal - invalid credentials error",
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "filled_form_fields": [{ "label": "Student ID", "value": "A00123456" }],
    "questions_completed": 0,
    "next_instruction": "Question 1 of 3 (2 remaining).\n\nIMPORTANT: You have access to all previously collected data in the response (title, description, filled_form_fields). Use this context to ask clarifying questions that gather ADDITIONAL information you need - NOT to re-collect form data already provided. Ask focused questions about context, urgency, troubleshooting history, or other relevant details.\n\nExamples of good clarifying questions:\n- \"When did this issue first start happening?\"\n- \"Have you tried anything to resolve this already?\"\n- \"Is this blocking your work right now?\"\n- \"Are there any error messages you're seeing?\"\n\nAfter they answer, you MUST call save_clarifying_question_answer(question=\"<your question>\", answer=\"<their answer>\") to record it."
}
```

**Does assistant know what to do?** ✅ Yes - `draft_stage` changed to `clarifying_questions`. `next_instruction` provides structured data in the response and guidance on asking clarifying questions that don't duplicate the form data already collected.

**Assistant Response:** "When did you first notice this problem - was it working fine before today?"

---

### Turn 6: User Answers Question 1

**User Message:** "It was working yesterday, the problem started this morning"

**Available Tools:** Same as Turn 2

**Assistant Decision:** User answered. Call `save_clarifying_question_answer` with question and answer.

**Tool Call:** `save_clarifying_question_answer(question="When did you first notice this problem - was it working fine before today?", answer="It was working yesterday, the problem started this morning")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "clarifying_questions",
    "type_name": "Password Reset",
    "title": "Cannot log into student portal - invalid credentials error",
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "filled_form_fields": [{ "label": "Student ID", "value": "A00123456" }],
    "questions_completed": 1,
    "next_instruction": "Question 2 of 3 (1 remaining).\n\nIMPORTANT: You have access to all previously collected data in the response (title, description, filled_form_fields). Use this context to ask clarifying questions that gather ADDITIONAL information you need - NOT to re-collect form data already provided. Ask focused questions about context, urgency, troubleshooting history, or other relevant details.\n\nExamples of good clarifying questions:\n- \"When did this issue first start happening?\"\n- \"Have you tried anything to resolve this already?\"\n- \"Is this blocking your work right now?\"\n- \"Are there any error messages you're seeing?\"\n\nAfter they answer, you MUST call save_clarifying_question_answer(question=\"<your question>\", answer=\"<their answer>\") to record it."
}
```

**Does assistant know what to do?** ✅ Yes - `questions_completed` is 1. `next_instruction` continues to provide context and guidance. The AI can see the Student ID was already collected in `filled_form_fields`.

**Assistant Response:** "Are you typing your password manually or using a saved/autofill password from your browser?"

---

### Turn 7: User Answers Question 2

**User Message:** "I'm using the saved password from Chrome"

**Available Tools:** Same as Turn 2

**Tool Call:** `save_clarifying_question_answer(question="Are you typing your password manually or using a saved/autofill password from your browser?", answer="I'm using the saved password from Chrome")`

**Tool Response:**

```json
{
    "success": true,
    "draft_stage": "clarifying_questions",
    "type_name": "Password Reset",
    "title": "Cannot log into student portal - invalid credentials error",
    "description": "When I enter my password it says 'Invalid credentials' but I know I'm using the right password. I've tried 5 times.",
    "filled_form_fields": [{ "label": "Student ID", "value": "A00123456" }],
    "questions_completed": 2,
    "next_instruction": "Question 3 of 3 (0 remaining).\n\nIMPORTANT: You have access to all previously collected data in the response (title, description, filled_form_fields). Use this context to ask clarifying questions that gather ADDITIONAL information you need - NOT to re-collect form data already provided. Ask focused questions about context, urgency, troubleshooting history, or other relevant details.\n\nExamples of good clarifying questions:\n- \"When did this issue first start happening?\"\n- \"Have you tried anything to resolve this already?\"\n- \"Is this blocking your work right now?\"\n- \"Are there any error messages you're seeing?\"\n\nAfter they answer, you MUST call save_clarifying_question_answer(question=\"<your question>\", answer=\"<their answer>\") to record it."
}
```

**Does assistant know what to do?** ✅ Yes - `questions_completed` is 2. This is the final clarifying question.

**Assistant Response:** "Have you tried logging in from a different browser or device to see if the issue persists?"

---

### Turn 8: User Answers Question 3

**User Message:** "No I haven't tried that"

**Available Tools:** Same as Turn 2, plus:

| Tool                           | Description                                                                                                                                                                                                                                               |
| ------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `check_ai_resolution_validity` | Checks if an AI resolution meets the confidence threshold. Call this after clarifying questions when you have a potential solution. DO NOT show the resolution to the user until this tool returns meets_threshold: true. Confidence score must be 0-100. |

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

**Does assistant know what to do?** ✅ Yes - `draft_stage` changed to `resolution`. `questions_completed` is 3. `next_instruction` tells them to formulate a resolution and call `check_ai_resolution_validity`. The AI has access to all collected data including form fields.

**Tool Call:** `check_ai_resolution_validity(confidence_score=82, proposed_answer="Based on your situation, the issue is likely with Chrome's saved password. Try these steps: 1) Go to Chrome settings > Passwords, 2) Find and delete the saved password for the student portal, 3) Go back to the login page and type your password manually. If this doesn't work, try the 'Forgot Password' link to reset it.")`

**Tool Response:**

```json
{
    "meets_threshold": true,
    "next_instruction": "Present your resolution to the user in a helpful way. End by asking: \"Does this help resolve your issue?\" Based on their response, call record_resolution_response(accepted=true) if they say yes, or record_resolution_response(accepted=false) if they say no or need more help."
}
```

**Does assistant know what to do?** ✅ Yes - `meets_threshold` is true. `next_instruction` tells them exactly how to present and what to ask.

**Assistant Response:** "Based on what you've described, the issue is likely with Chrome's saved password being out of date. Here's what I'd suggest:

1. Go to Chrome settings > Passwords
2. Find and delete the saved password for the student portal
3. Go back to the login page and type your password manually

If that doesn't work, try the 'Forgot Password' link to reset it.

Does this help resolve your issue?"

---

### Turn 9: User Rejects Resolution

**User Message:** "No, I tried that and it still doesn't work"

**Available Tools:** Same as Turn 8, plus:

| Tool                         | Description                                                                                                                           |
| ---------------------------- | ------------------------------------------------------------------------------------------------------------------------------------- |
| `record_resolution_response` | Records the user's response to your resolution attempt. Call this after presenting a resolution and getting explicit yes/no feedback. |

**Assistant Decision:** User said resolution didn't work. Call `record_resolution_response` with accepted=false.

**Tool Call:** `record_resolution_response(accepted=false)`

**Tool Response:**

```json
{
    "success": true,
    "request_number": "SR-2026-00542",
    "next_instruction": "Tell user: \"Your request number is SR-2026-00542. A team member will follow up to help resolve this.\""
}
```

**Does assistant know what to do?** ✅ Yes - `next_instruction` provides the exact message to convey.

**Assistant Response:** "I'm sorry that didn't work. I've submitted your request to our support team. Your request number is SR-2026-00542. A team member will follow up with you soon to help resolve this login issue."

---

### Summary: Information Flow

At each turn, the assistant knows what to do because:

1. **Tool descriptions** explain WHEN to use each tool (after user responds, not before)
2. **Tool responses** include `next_instruction` with:
    - Conversational prompts to use (e.g., "Ask: 'Can you describe what's happening?'")
    - Explicit tool calls with parameters (e.g., `update_form_field(field_id="...", value="...")`)
    - Guidance on question types for clarifying questions
    - Contextual optional field prompts (skipped fields or all remaining fields before description)
3. **Draft status** is stage-appropriate:
    - `data_collection`: `missing_required_fields`, `missing_optional_fields` with field_id, label, type; `has_custom_form_fields` flag
    - `clarifying_questions`/`resolution`: `questions_completed` counter, `title`, `description`, and `filled_form_fields` array with structured data (signatures show as `[Signature provided]`, checkboxes as `Yes`/`No`, all values limited to 255 chars)
4. **Context-aware prompts**:
    - If form has custom fields, description prompt says "anything else to add" (since form captured details)
    - If no custom fields, description prompt asks to "describe what's happening"
    - Complex fields (select, date): AI calls `show_field_input` AND asks a natural question in same response
    - Optional fields surfaced contextually: skipped fields during collection, all remaining before description
5. **Stage transitions** are automatic and visible in the response (`draft_stage` changes)
6. **Minimal context pollution** - only relevant data for the current stage is included
7. **Multi-step form ordering** - Fields are collected in step order (by `sort`), ensuring logical flow through form sections

The assistant never needs to call `get_draft_status` separately because all action tools return the full draft status. The only exception is when resuming a conversation with an existing draft, where `get_draft_status` provides the initial context.
