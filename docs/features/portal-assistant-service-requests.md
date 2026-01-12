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
- 13 specialized tools for type selection, data collection, enrichment, and submission
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

Service requests progress through sequential stages. The stage is **derived from the current state** of the draft:

1. **type_selection** - Type has been selected (draft exists) but priority not yet assigned (`priority_id = null`)
2. **data_collection** - Priority assigned, collecting required fields (title, description, form fields)
3. **clarifying_questions** - All required fields filled, collecting 3 clarifying question/answer pairs
4. **resolution** - 3 clarifying questions completed, evaluating and presenting AI resolution (if enabled)

**Draft Creation:** The draft is created when the user selects a service request type via the `show_type_selector` widget. Before type selection, no draft exists.

**Data Collection Order:** fields (if any) → description → title → **priority** (last step before clarifying questions)

The draft starts in `type_selection` stage (priority not assigned). Once priority is selected, the stage becomes `data_collection` if there are still missing required fields, or `clarifying_questions` if all fields are complete.

Stage determination logic:
```php
if (!$draft->priority_id) return 'type_selection';
if (hasMissingRequiredFields($draft)) return 'data_collection';
if (clarifyingQuestionsCount($draft) < 3) return 'clarifying_questions';
return 'resolution';
```

When submitted (finalized), `is_draft = false` and `service_request_number` is generated. Status set to "Closed" if resolution accepted, "New" if rejected or resolution disabled/below threshold.

## Progressive Tool Disclosure

Tools unlock sequentially as prerequisites are met, preventing users from being overwhelmed by simultaneous choices while maintaining flexibility to edit previous responses.

### Data Collection Stage

**Collection Order:** Custom form fields (if present) → Description → Title → Priority

**Tool Availability:**
- `update_form_field`, `show_field_input`: If service request type defines custom fields
- `update_description`: After required form fields filled (or immediately if no custom fields)
- `update_title`: After description provided
- `show_priority_selector`: After title provided

**Phase Transition:** When priority is selected and all required fields are filled, `get_draft_status` detects the stage is now `clarifying_questions` (derived from state) and instructs AI to begin asking questions.

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
- Creates draft if none exists for thread
- Returns hierarchical tree of available service request types
- Draft initially has no priority (phase: 'type_selection')

**show_type_selector**
- Emits `PortalAssistantActionRequest` event with type tree and optional suggestion
- Frontend displays widget, hides main chat input
- User selection processed via dedicated controller

### Data Collection Tools

**get_draft_status**
- Returns complete draft state including form structure, filled fields, missing requirements
- Derives current phase from draft state
- Provides phase-specific instructions
- Detects when all required data filled and instructs transition to clarifying questions

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

Users may change service request type during data collection:

**Switching to Different Type:**
- New draft created for selected type
- Old draft retained (not deleted)
- `current_service_request_draft_id` updated to new draft
- Workflow resets to `data_collection` phase (no priority yet)

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
Previous iterations included separate validation and submission tools. Current design uses automatic stage transitions and resolution tools that handle submission internally, eliminating redundant steps.

**Validation:**
When all required fields are filled, `get_draft_status` automatically advances to the next draft stage. This eliminates redundant validation calls and simplifies the AI interaction model.

**Field Order Flexibility:**
While the collection order is fields → description → title → priority, the system validates all required fields are present regardless of order. This accommodates edge cases where AI collects information out of sequence.

---

## Example Conversation: Complex Form Fields

This example shows a service request type with multiple required and optional custom fields, demonstrating how the AI handles optional field collection intelligently.

### Initial Request

**User:** "I need to request access to the Biology lab for my research project"

**AI calls `fetch_service_request_types`**
- Creates draft
- Identifies "Lab Access Request" type based on keywords

**AI calls `show_type_selector`**
- Suggests "Lab Access Request"
- Widget displays with suggestion

**AI responds:** "I think this is a Lab Access Request. Please confirm."

**User clicks "Lab Access Request"**

**Backend processes:**
- Updates draft with selected type
- Creates ServiceRequestFormSubmission
- Sets default priority to "Medium"
- Phase becomes 'data_collection' (has priority now, but missing required fields)
- Type has 3 required custom fields: Building Name, Lab Number, Faculty Supervisor
- Type has 2 optional custom fields: Preferred Start Date, Research Project Title
- Sends developer message to AI: "User selected 'Lab Access Request'. Type has 3 required fields and 2 optional fields."

---

### Required Fields Collection

**AI calls `get_draft_status`**
- Returns: 3 required custom fields, plus description/title/priority
- Instruction: "Ask for Building Name first"

**AI responds:** "Which building is the lab in?"

**User:** "Science Center"

**AI calls `update_form_field`** (Building Name: "Science Center")
- Saves "Science Center" to Building Name field
- Returns success

**Developer message:** "Field 'Building Name' updated"

---

**AI calls `get_draft_status`**
- Returns: Building Name filled, 2 more required fields
- Instruction: "Ask for Lab Number"

**AI responds:** "What is the lab number?"

**User:** "Lab 204"

**AI calls `update_form_field`** (Lab Number: "Lab 204")
- Returns success

**Developer message:** "Field 'Lab Number' updated"

---

**AI calls `get_draft_status`**
- Returns: 2 fields filled, 1 required field remaining
- Instruction: "Ask for Faculty Supervisor"

**AI responds:** "Who is your faculty supervisor for this research?"

**User:** "Dr. Sarah Johnson"

**AI calls `update_form_field`** (Faculty Supervisor: "Dr. Sarah Johnson")
- Returns success

**Developer message:** "Field 'Faculty Supervisor' updated"

---

### Optional Fields Evaluation

**AI calls `get_draft_status`**
- Returns: All 3 required custom fields filled, but description/title/priority still needed
- Optional fields available: Preferred Start Date, Research Project Title
- Instruction: "All required fields are filled. There are 2 optional field(s) available: Preferred Start Date, Research Project Title. Based on the conversation context, decide if any would be helpful to collect. If yes, ask for ONE optional field. If no optional fields seem relevant, call get_draft_status again to proceed to description."

**AI internally evaluates:**
- User mentioned "research project" → Research Project Title seems relevant
- No time urgency mentioned → Preferred Start Date probably not needed
- Decision: Collect Research Project Title, skip Preferred Start Date

**AI responds:** "What is your research project title? This will help the lab coordinator prepare for your access."

**User:** "Effects of Temperature Variation on Bacterial Growth Rates"

**AI calls `update_form_field`** (Research Project Title: "Effects of Temperature...")
- Returns success

**Developer message:** "Field 'Research Project Title' updated"

---

**AI calls `get_draft_status`**
- Returns: 1 optional field remaining (Preferred Start Date), description/title/priority still needed
- Instruction: "There are 1 optional field(s) available: Preferred Start Date. Based on the conversation context, decide if any would be helpful to collect. If yes, ask for ONE optional field. If no optional fields seem relevant, call get_draft_status again to proceed to description."

**AI internally evaluates:**
- Preferred Start Date: User hasn't mentioned timing, not critical for lab access
- Decision: Skip this optional field, move to description

**AI calls `get_draft_status`** (to proceed to description)
- Returns: Optional fields done, now need description
- Instruction: "Ask for detailed description of their issue or request"

**AI responds:** "Please describe what equipment or resources you'll need in the lab."

**User:** "I need access to the incubators and microscopes for bacterial culture observation. I'll be running experiments twice a week."

**AI calls `update_description`**
- Saves description to draft.close_details
- Returns success

---

### Title & Priority

**AI calls `get_draft_status`**
- Returns: Description filled, now need title
- Instruction: "Suggest a title based on all collected information"

**AI responds:** "I suggest: 'Lab access for bacterial growth research in Science Center Lab 204' - Is this okay?"

**User:** "Yes"

**AI calls `update_title`**
- Saves title to draft
- Returns success

---

**AI calls `get_draft_status`**
- Returns: Only priority missing now
- Instruction: "Call show_priority_selector to display priority widget"

**AI calls `show_priority_selector`**
- Widget displays: High, Medium, Low options
- Chat input hidden

**AI responds:** "Please select the priority level for your request."

**User clicks "Medium"**

**Backend processes:**
- Updates draft.priority_id to "Medium"
- Sends developer message: "User selected priority 'Medium'"

---

### Auto-Transition & Questions

**AI calls `get_draft_status`**
- Returns: All required fields filled (collected 1 of 2 optional fields)
- **Phase is now 'clarifying_questions' (derived from all required fields being filled)**
- Instruction: "All required information has been collected. Now ask the first of 3 clarifying questions to better understand the user's issue. Make the question specific to their situation based on the information provided."

**AI responds:** "How long do you expect to need lab access for your research?"

**User:** "About 3 months"

**AI calls `save_clarifying_question`**
- Saves Q1: "How long do you expect to need lab access for your research?"
- Saves A1: "About 3 months"
- Returns: completed: 1, remaining: 2

---

**AI responds:** "Will you need any specialized training for the equipment before starting?"

**User:** "No, I'm already certified for the equipment from last semester"

**AI calls `save_clarifying_question`**
- Saves Q2 and A2
- Returns: completed: 2, remaining: 1

---

**AI responds:** "Are there specific days or times that work best for your lab sessions?"

**User:** "Tuesday and Thursday afternoons would be ideal"

**AI calls `save_clarifying_question`**
- Saves Q3 and A3 as updates
- Returns: completed: 3, remaining: 0
- **Phase is now 'resolution' (derived from 3 questions in updates)**

---

### Resolution & Submission

**AI calls `check_ai_resolution_validity`**
- confidence: 75
- proposed_answer: "Your lab access request needs approval from your faculty supervisor and the lab coordinator. I can guide you through the next steps..."

**Tool evaluates:**
- Confidence 75 meets configured threshold (70)
- Stores resolution in draft.ai_resolution with meets_threshold: true
- Does NOT submit yet

**Tool returns:** "Confidence meets threshold. Present the proposed resolution to the user and ask if it resolves their issue."

**AI responds:** "Your lab access request needs approval from your faculty supervisor and the lab coordinator. I can guide you through the next steps... Does this resolve your issue?"

**User:** "Yes, that helps!"

**AI calls `record_resolution_response`**
- accepted: true
- Auto-submits with status: "Closed" (resolved, no team assignment needed)
- Request number: SR-2026-00543

**AI responds:** "Great! Your request has been marked as resolved. Request number: SR-2026-00543"

---

### Final State

**Service Request Created:**
- Number: SR-2026-00543
- Status: Resolved (Closed) - no team assignment needed
- Custom Fields Collected:
  - Building Name: "Science Center" (required) ✓
  - Lab Number: "Lab 204" (required) ✓
  - Faculty Supervisor: "Dr. Sarah Johnson" (required) ✓
  - Research Project Title: "Effects of Temperature..." (optional) ✓
  - Preferred Start Date: (optional, skipped by AI) ✗
- AI made intelligent decision to collect relevant optional field but skip irrelevant one

---

## Example Conversation: Password Reset

This example demonstrates a simpler flow with one custom field, clarifying questions, and rejected resolution.

### Initial Request

**User:** "I can't log into the student portal, it keeps saying my password is wrong"

**AI calls `fetch_service_request_types`**
- Creates draft (phase: type_selection - no priority yet)
- Returns tree of available service request types
- AI analyzes: user mentioned "password" → identifies "Password Reset" type

**AI calls `show_type_selector`**
- Passes suggested_type_id for "Password Reset"
- Widget displays with suggestion highlighted
- Chat input hidden, "Cancel" link shown

**AI responds:** "I think this might be a Password Reset request. Please confirm or select a different type."

---

### Type Selection (via widget)

**User clicks "Password Reset" in widget**

**Backend processes selection:**
- Updates draft with selected type
- Creates ServiceRequestFormSubmission
- Sets default priority to "Medium"
- Phase becomes 'data_collection' (has priority now, but missing fields)
- Sends developer message to AI: "User selected 'Password Reset'. Type has 1 custom field: 'Student ID Number' (required, text)."

---

### Data Collection - Custom Field

**AI calls `get_draft_status`**
- Returns: 1 required field (Student ID), plus description/title/priority needed
- Instruction: "Ask for Student ID Number first"

**AI responds:** "What is your Student ID Number?"

**User:** "A00123456"

**AI calls `update_form_field`**
- Saves "A00123456" to Student ID field
- Returns success

---

### Data Collection - Description

**AI calls `get_draft_status`**
- Returns: Student ID filled, now need description
- Instruction: "Ask for detailed description"

**AI responds:** "Please describe what's happening when you try to log in."

**User:** "When I type my password and click login, it shows 'Invalid credentials' but I know my password is correct. I've tried 5 times today and it's the same password I used yesterday."

**AI calls `update_description`**
- Saves description to draft.close_details
- Returns success

---

### Data Collection - Title

**AI calls `get_draft_status`**
- Returns: Student ID and description filled, now need title
- Instruction: "Suggest a title based on collected info"

**AI responds:** "Based on your description, I suggest: 'Cannot log into student portal with correct password' - Is this okay?"

**User:** "That's good"

**AI calls `update_title`**
- Saves title to draft
- Returns success

---

### Data Collection - Priority

**AI calls `get_draft_status`**
- Returns: Only priority missing now
- Instruction: "Show priority selector"

**AI calls `show_priority_selector`**
- Widget displays: High, Medium, Low options
- Chat input hidden

**AI responds:** "Please select the priority level for your request."

**User clicks "High" in widget**

**Backend processes selection:**
- Updates draft.priority_id to "High"
- Sends developer message: "User selected priority 'High'"

---

### Auto-Transition to Clarifying Questions

**AI calls `get_draft_status`**
- Returns: All required fields filled
- **Phase is now 'clarifying_questions' (derived from all required fields being filled)**
- Instruction: "Ask first clarifying question"

---

### Clarifying Questions (3 required)

**AI responds:** "You mentioned you used the same password successfully yesterday. Did you make any changes to your account or receive any emails about your account since then?"

**User:** "No, nothing changed and I didn't get any emails"

**AI calls `save_clarifying_question`**
- Saves Q1 and A1 to draft.clarifying_questions array
- Returns: completed: 1, remaining: 2

---

**AI responds:** "Are you trying to log in from a different device or browser than you normally use?"

**User:** "Same laptop and same Chrome browser I always use"

**AI calls `save_clarifying_question`**
- Saves Q2 and A2
- Returns: completed: 2, remaining: 1

---

**AI responds:** "When you enter your password, are you typing it manually or using a saved password from your browser?"

**User:** "I'm using the saved password from Chrome"

**AI calls `save_clarifying_question`**
- Saves Q3 and A3 as two updates (question and answer)
- Returns: completed: 3, remaining: 0
- **Phase is now 'resolution' (derived from 3 questions in updates)**
- Instruction: "Call check_ai_resolution_validity with your proposed solution"

---

### AI Resolution Attempt

**AI calls `check_ai_resolution_validity`**
- Confidence score: 85 (out of 100)
- Proposed answer: Step-by-step guide to clear saved password and type manually
- Tool compares: 85 >= 70 (threshold) = **meets threshold**
- Creates resolution update with `update_type = 'ai_resolution_proposed'`
- Stores confidence score: 85 in `ai_resolution_confidence_score`
- Returns: "Present resolution to user and wait for response"

**AI responds with the resolution:**
"Based on your situation, the issue is likely Chrome's saved password being incorrect. Here's how to fix it:

1. Go to the student portal login page
2. Clear your browser's saved password for this site
3. Type your password manually instead of using autofill
4. Try logging in

If this works, you can save the password again. The autofill sometimes saves an incorrect version after failed attempts.

Did this solve your problem?"

---

### User Rejects Resolution

**User:** "I tried it but I still can't log in. Same error message."

**AI calls `record_resolution_response`**
- Passes: accepted = false
- Tool internally:
  - Creates response update with `update_type = 'ai_resolution_response'`
  - Sets is_ai_resolution_attempted = true
  - Sets is_ai_resolution_successful = false
  - Sets status to "New" (Open for human review)
  - Generates service_request_number: "SR-2026-00542"
  - Sets is_draft = false
  - Assigns to team member based on type's assignment strategy
- Returns: request_number, status: open

**AI responds:** "I've submitted your request for human review. Your request number is **SR-2026-00542**. A team member from IT Support will follow up with you soon to help resolve your login issue."

---

### Final Result

**Service Request Created:**
- Number: SR-2026-00542
- Status: New (Open)
- Title: "Cannot log into student portal with correct password"
- Priority: High
- Assigned to: IT Support team member
- Custom Fields: Student ID = A00123456
- Updates: 8 total
  - 6 clarifying Q&A updates (3 questions, 3 answers with `update_type`)
  - 1 AI resolution proposed update (`update_type = 'ai_resolution_proposed'`)
  - 1 User rejection update (`update_type = 'ai_resolution_response'`)
- AI Resolution: Attempted (85% confidence), presented to user, rejected
- Timeline: All questions, answers, and resolution visible to team

**Alternative Outcomes:**

*If confidence was 65 (below 70 threshold):*
- Tool would auto-submit without showing resolution to user
- Resolution stored as internal note only
- User told: "Request submitted for review" (no resolution shown)

*If user accepted the resolution:*
- Status would be "Resolved" (Closed)
- No team assignment (no human review needed)
- User told: "Issue marked as resolved, request number for your records"
