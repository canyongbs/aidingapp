# Portal Assistant Service Request Submission

## Overview

The Portal Assistant provides an AI-powered conversational interface for users to submit service requests. The assistant guides users through the entire submission process, collecting required information, asking clarifying questions, and optionally attempting to resolve issues automatically.

## Architecture

### Key Components

- **SendMessage Job** (`app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`): Main orchestration job that handles AI streaming and tool access control
- **Portal Assistant Tools** (`app-modules/ai/src/Tools/PortalAssistant/`): 13 specialized tools for different phases of the workflow
- **Service Request Drafts**: Multiple draft service request records can exist per thread, with the thread tracking which one is currently active
- **Current Draft Tracking**: The `PortalAssistantThread` model has a `current_service_request_draft_id` field that points to the active draft

### Draft Lifecycle

Drafts are created and managed based on service request type selection:

1. **No Draft Initially**: When a user starts a conversation, no draft exists yet
2. **Type Selection Creates Draft**: When user selects a type, a new draft is created for that specific type
3. **Type Switching**: If the user changes their mind and selects a different type:
   - A new draft is created for the new type
   - The old draft remains in the system (not deleted)
   - Tools reset to data_collection phase (progress doesn't carry over between types)
   - `current_service_request_draft_id` on the thread is updated to point to the new draft
4. **Returning to Previous Type**: If user switches back to a previously selected type:
   - The system finds the existing draft for that type
   - Switches `current_service_request_draft_id` back to that draft
   - Previously collected information (title, description, fields) is restored
   - User can continue where they left off with that type

This design allows users to explore multiple service request types without losing work, while ensuring type-specific form fields and data don't mix.

### Workflow Phases

The submission process follows a strict sequential workflow:

1. **data_collection** - Collects all required information in the following order:
   - Custom form fields (if the type has any)
   - Description (detailed explanation)
   - Title (AI suggests based on all collected information, user can accept/modify)
   - Priority (urgency level)
   - Phase starts after type selection
2. **clarifying_questions** - AI asks 3 clarifying questions to better understand the issue
3. **resolution** - AI attempts resolution (if enabled) or submits for human review

## Progressive Tool Unlocking

The system implements a "one-way gate" approach to prevent the AI from overwhelming users:

- Tools unlock progressively as prerequisites are met
- Once unlocked, tools remain available (users can edit previous fields)
- Users cannot skip ahead to future steps
- This prevents "decision fatigue" where the AI tries to fill all fields at once

### Data Collection Phase Unlocking

The unlocking sequence follows a consistent flow regardless of whether the service request type has custom form fields:

```
Type Selected
    ↓
update_form_field, show_field_input for custom fields (unlocked if type has custom fields)
    ↓ (after all required custom fields filled, or immediately if no custom fields)
update_description (unlocked) - User explains issue/request in detail
    ↓ (after description saved - REQUIRED field)
update_title (unlocked) - AI suggests title based on description + form fields, user confirms/edits
    ↓ (after title saved)
show_priority_selector (unlocked)
    ↓ (after priority selected)
submit_service_request (advances to next phase)
```

**Field Collection Order:**
1. **Custom form fields** (if the type has any) - Structured data specific to the request type
2. **Description** (always required) - Detailed explanation of the issue or request
3. **Title** (always required) - AI suggests based on all information collected so far
4. **Priority** (always required) - User selects urgency level

**Why This Order?**

1. **Structured Data First**: If the type has custom fields, collect them first to provide context for the description
2. **Description Before Title**: Users explain their issue fully before being asked to summarize it with a title
3. **Title Before Priority**: AI can suggest a title with confidence when it has all the context (form fields + description)
4. **Priority Last**: Priority doesn't affect the description or title content - it's purely about urgency and routing

### Type Switching Behavior

Users can change their mind about which type of service request they want to submit:

**Switching to a New Type:**
- User selects a different type than they're currently working on
- System creates a new draft for the new type
- Old draft is preserved (not deleted) in the database
- Progress resets - tools return to data_collection phase
- Thread's `current_service_request_draft_id` updates to the new draft
- User starts fresh with the new type's form fields

**Switching Back to a Previous Type:**
- User selects a type they've already started
- System finds the existing draft for that type
- Thread's `current_service_request_draft_id` switches back to the old draft
- Previously filled information is restored (title, description, form fields)
- User can continue where they left off

**Why This Design:**
- Prevents mixing data from different service request types
- Each type may have completely different form fields
- Allows users to explore options without losing work
- Keeps separate contexts for separate request types

## Available Tools

### Always Available Tools

These tools are accessible throughout the entire workflow:

#### Fetch Service Request Types
Retrieves all available service request types organized by category. Does NOT create a draft - drafts are only created when the user actually selects a type. Returns a hierarchical tree structure of categories and their associated types, along with whether a draft currently exists and its workflow phase. The AI typically follows this by showing the type selector to the user.

#### Show Type Selector
Displays a UI widget that allows the user to select which type of service request they want to submit. The AI can optionally suggest a specific type based on the conversation context, which will be highlighted in the interface. This emits an event that the frontend listens for to render the appropriate selection interface.

#### Get Draft Status
Returns comprehensive information about the current state of the service request draft. This includes what's been filled in (title, description, form fields), what's still missing, the current workflow phase, available priorities, the structure of any custom form fields, and instructions for what should happen next. The AI uses this frequently to understand what information still needs to be collected.

### Data Collection Phase Tools

These tools unlock progressively as the user provides information:

#### Update Description
Saves detailed information about the issue or request. This field is REQUIRED and cannot be skipped. The AI asks the user to describe their issue or request in detail. This always comes after custom form fields (if any exist) but before the title.

Saves immediately without confirmation. Saves to the `close_details` database column.

**When Available**: After all required custom fields are filled (if type has custom fields), OR immediately after type selection (if type has no custom fields)

#### Show Field Input
Displays a specialized UI widget for custom form fields that require user selection or complex input rather than free text. This includes selects, radio buttons, checkboxes, date pickers, phone number inputs, address fields, and file uploads. The system automatically prompts the user with the appropriate interface and handles the input. Emits an event with the field configuration that the frontend renders.

**When Available**: Immediately after type selection (if type has custom fields)

#### Update Form Field
Updates the value of simple text-based custom form fields like text inputs, text areas, numbers, and email addresses. Each service request type may have different custom fields defined, and this tool handles the straightforward ones. The AI must ask the user for each field value individually and save immediately. For complex fields like dropdowns or date pickers, the AI should use Show Field Input instead.

**When Available**: Immediately after type selection (if type has custom fields)

#### Show Priority Selector
Displays a UI widget that allows the user to select the priority level for their service request. Each service request type has its own set of available priorities (typically High, Medium, Low, but can vary). The system automatically prompts the user with a selector showing the available priority options, and the user's selection is submitted back to the backend via the metadata mechanism.

**When Available**: After title is saved

#### Update Title
Saves the title for the service request. When title is needed, the AI suggests a concise, descriptive title based on all collected information so far (form fields and description) and asks the user to confirm or edit it. Once the user provides/confirms the title, the AI saves it immediately without asking for additional confirmation.

**When Available**: After description is saved

#### Submit Service Request
Validates that all required information has been collected and advances the workflow to the clarifying questions phase. This is always callable, but if validation fails, it returns detailed information about what's still missing. Once validation passes, the draft transitions to the next phase where the AI will ask clarifying questions.

**When Available**: Always available in data_collection phase (validation will catch errors)

### Clarifying Questions Phase Tools

#### Save Clarifying Question
Records both the question the AI asked and the user's answer as a pair. This must be called exactly three times during the clarifying questions phase. Each call increments the counter, and once all three questions are answered, the workflow automatically advances to the resolution phase. The questions and answers are stored and later become part of the finalized service request record for staff reference.

**Requirement**: Must be called exactly 3 times during this phase

#### Finalize Service Request
Submits the service request directly for human review, bypassing any AI resolution attempt. This is used when the AI has low confidence, when AI resolution is disabled, or when the user declines the AI's proposed resolution. Creates service request update records from the clarifying questions, generates a service request number, sets the status to "New", marks the draft as finalized, and assigns the request to an appropriate team member based on the type's assignment rules.

**When Available**: During clarifying_questions or resolution phase

### Resolution Phase Tools

These tools are only available if AI resolution is enabled in system settings:

#### Submit AI Resolution
The AI uses this to propose a solution with a confidence score from 0-100. The system compares the confidence score against a configurable threshold. If the score meets or exceeds the threshold, the AI is instructed to present the resolution to the user. If below threshold, the AI is told not to present it and to finalize the request for human review instead. The resolution text and confidence score are stored for analytics and staff review.

**Note**: Confidence threshold is configurable in AiResolutionSettings

#### Record Resolution Response
After presenting a resolution to the user, the AI calls this with whether the user accepted it (said it solved their problem) or declined it (said it didn't help). If accepted, the service request is marked as resolved with a "Closed" status. If declined, it's set to "New" status for staff follow-up. In both cases, service request update records are created documenting the resolution attempt and outcome, a service request number is generated, and the request is finalized and assigned appropriately.

**Side Effects**: Creates detailed service request updates, changes status based on acceptance, generates service request number, finalizes draft, and assigns to team member

## Example Conversation Flow

### Scenario: Student Cannot Access Portal

```
User: "I can't log into the student portal"

AI: [calls Fetch Service Request Types]
    Passes: Nothing
    Returns: A hierarchical tree of all available service request types organized by categories 
             (e.g., Technical Support > Password Reset, Account Issues > Access Problems)
             Returns that no draft exists yet (has_draft: false)
             Includes instruction to call Show Type Selector next
    
AI: [calls Show Type Selector]
    Passes: A suggested type ID for "Password Reset" based on keywords in user's message
    Returns: Confirmation that the widget was displayed
             The UI now shows the type selector with "Password Reset" highlighted as a suggestion
    
User: [selects "Password Reset" type via UI]
    System creates a new draft for "Password Reset" type in "data_collection" phase
    Sets thread's current_service_request_draft_id to point to this new draft
    Creates form submission record and associates default priority
    
AI: [calls Get Draft Status]
    Passes: Nothing
    Returns: Full state showing the "Password Reset" type has one required custom field: "Student ID"
             Returns instruction: "Ask the user: 'Student ID' Then STOP..."
    
AI: "Student ID"

User: "A12345678"

AI: [calls Update Form Field]
    Passes: The field ID (UUID of the Student ID field) and value "A12345678"
    Returns: Success confirmation with field ID and the field's human-readable label "Student ID"
             Includes instruction that field was saved successfully
             
AI: [calls Get Draft Status]
    Passes: Nothing
    Returns: All required custom fields are now filled
             Returns instruction: "Ask the user to describe the issue in detail"
    
AI: "Please describe the issue you're experiencing. What happens when you try to log in?"

User: "When I try to log in with my username and password, I get an error message saying invalid credentials. I'm sure my password is correct."

AI: [calls Update Description]
    Passes: The description text explaining the error
    Returns: Success confirmation that description was saved to close_details field
             Includes instruction: "Suggest a concise, descriptive title and ask user to confirm or edit it"
    
AI: "Based on your issue, I suggest the title: 'Cannot Access Student Portal'

Would you like to use this title, or would you prefer to change it?"

User: "That's perfect!"

AI: [calls Update Title]
    Passes: The confirmed title "Cannot Access Student Portal"
    Returns: Success confirmation that title was saved
             Includes instruction: "Call show_priority_selector to display the priority options"
    
AI: [calls Show Priority Selector]
    Passes: Nothing (gets priority options from draft's type)
    Returns: Confirmation that the priority selector widget was displayed
             UI shows available priorities: High, Medium, Low (specific to Password Reset type)
             
User: [selects "Medium" priority via UI]
    System saves priority selection via metadata.type = 'priority_selection', metadata.priority_id
    Processes the selection and updates draft's priority field
    
AI: [calls Submit Service Request]
    Passes: Nothing
    Returns: Validation passed - all required fields are filled
             Draft workflow_phase advanced to "clarifying_questions"
             Returns instruction: "Ask the user 3 clarifying questions specific to their request"
    
AI: "When did you last successfully log in to the portal?"

User: "About two weeks ago"

AI: [calls Save Clarifying Question]
    Passes: Question "When did you last successfully log in to the portal?" 
            Answer "About two weeks ago"
    Returns: Success with counters showing 1 completed, 2 remaining
             Instruction to continue asking questions
    
AI: "Have you recently changed your password or had it reset?"

User: "No, I haven't changed anything"

AI: [calls Save Clarifying Question]
    Passes: Question "Have you recently changed your password or had it reset?"
            Answer "No, I haven't changed anything"
    Returns: Success with counters showing 2 completed, 1 remaining
             Instruction to continue asking questions
    
AI: "Are you able to access any other campus systems with the same credentials?"

User: "Yes, I can check my email just fine"

AI: [calls Save Clarifying Question]
    Passes: Question "Are you able to access any other campus systems with the same credentials?"
            Answer "Yes, I can check my email just fine"
    Returns: Success with counters showing 3 completed, 0 remaining
             Draft workflow_phase automatically advanced to "resolution"
             Returns that AI resolution is enabled
             Returns instruction: "Attempt to resolve using submit_ai_resolution with your confidence score and proposed answer"
    
AI: [calls Submit AI Resolution]
    Passes: Confidence score of 75 (out of 100)
            Proposed answer text explaining the browser cache solution
    Returns: Confidence score 75 meets the threshold of 70
             Returns instruction: "Present your resolution to the user and ask if it solved their problem"
    
AI: "Based on your situation, I believe the issue may be with your browser cache. Try clearing your browser's cache and cookies, then attempt to log in again using Chrome or Firefox. If you're using saved passwords, try typing your password manually instead.

Does this resolve your issue?"

User: "Yes! That worked, thank you!"

AI: [calls Record Resolution Response]
    Passes: Boolean true (user accepted the resolution)
    Returns: Success with generated request number "SR-2026-00123"
             Resolution was accepted so status set to "Closed" (resolved)
             Draft marked as finalized (is_draft = false)
             Request assigned to appropriate team member
             Returns instruction: "Tell the user their issue has been marked as resolved. Provide the request number..."
    
AI: "Great! Your issue has been marked as resolved. Your request number is SR-2026-00123 for your records. If you experience any other issues, feel free to reach out again."
```

### Tool Call Timeline for Above Conversation

```
1. Fetch Service Request Types
   → Returns hierarchical tree of all available types organized by category

2. Show Type Selector
   → Suggests "Password Reset" type and displays selection UI to user

3. [User selects type via UI - no tool call]

4. Get Draft Status
   → Checks current state and discovers "Student ID" custom field is required

5. Update Form Field
   → Saves "A12345678" to the Student ID field

6. Get Draft Status
   → Checks current state and discovers all custom fields filled, description is next

7. Update Description
   → Saves the description explaining the login error

8. [AI suggests title in response text - no tool call]

9. Update Title
   → Saves user's confirmed title "Cannot Access Student Portal"

10. Show Priority Selector
    → Displays priority selector widget with High, Medium, Low options

11. [User selects "Medium" priority via UI - no tool call]

12. Submit Service Request
    → Validates all required data and advances to clarifying questions phase

13. Save Clarifying Question
    → Records question about last successful login and user's answer (1 of 3)

14. Save Clarifying Question
    → Records question about password changes and user's answer (2 of 3)

15. Save Clarifying Question
    → Records question about other systems and user's answer (3 of 3)
    → Automatically advances to resolution phase

16. Submit AI Resolution
    → Proposes browser cache solution with 75% confidence
    → Confidence exceeds threshold, instructed to present to user

17. Record Resolution Response
    → User accepted the resolution
    → Marks request as resolved with status "Closed"
    → Generates request number SR-2026-00123
```

### Scenario: General Question (No Custom Form Fields)

This example demonstrates the workflow when a service request type has **NO** custom form fields. The flow is the same as types with custom fields, just skipping the form field step:

```
User: "I need help understanding the financial aid process"

AI: [calls Fetch Service Request Types]
    Passes: Nothing
    Returns: Hierarchical tree of all available service request types
             Returns that no draft exists yet (has_draft: false)
             Includes instruction to call Show Type Selector next
    
AI: [calls Show Type Selector]
    Passes: A suggested type ID for "General Question" based on keywords in user's message
    Returns: Confirmation that the widget was displayed
             The UI now shows the type selector with "General Question" highlighted as a suggestion
    
User: [selects "General Question" type via UI]
    System creates a new draft for "General Question" type in "data_collection" phase
    Sets thread's current_service_request_draft_id to point to this new draft
    Creates form submission record (with no custom fields) and associates default priority
    
AI: [calls Get Draft Status]
    Passes: Nothing
    Returns: Full state showing the "General Question" type has NO custom fields
             Returns instruction: "Please describe your issue or request in detail"
    
AI: "Please describe your question about financial aid. What specifically do you need help with?"

User: "I'm a transfer student starting in the fall and I'm confused about the deadlines and what documents I need to submit. I haven't filled out my FAFSA yet and I'm not sure if the process is different for transfer students."

AI: [calls Update Description]
    Passes: The detailed description of the user's question
    Returns: Success confirmation that description was saved to close_details field
             Includes instruction: "Suggest a concise, descriptive title and ask user to confirm or edit it"
    
AI: "Based on your question, I suggest the title: 'Financial aid process guidance for transfer student'

Would you like to use this title, or would you prefer something different?"

User: "That's perfect"

AI: [calls Update Title]
    Passes: The confirmed title "Financial aid process guidance for transfer student"
    Returns: Success confirmation that title was saved
             Includes instruction: "Call show_priority_selector to display the priority options"
    
AI: [calls Show Priority Selector]
    Passes: Nothing (gets priority options from draft's type)
    Returns: Confirmation that the priority selector widget was displayed
             UI shows available priorities: High, Medium, Low
             
User: [selects "Medium" priority via UI]
    System saves priority selection via metadata.type = 'priority_selection', metadata.priority_id
    Processes the selection and updates draft's priority field
    
AI: [calls Submit Service Request]
    Passes: Nothing
    Returns: Validation passed - all required fields are filled (description, priority, title)
             No custom form fields to validate
             Draft workflow_phase advanced to "clarifying_questions"
             Returns instruction: "Ask the user 3 clarifying questions specific to their request"
    
AI: "To help me provide the best guidance, let me ask a few clarifying questions. 

First, have you already been admitted to the university as a transfer student?"

User: "Yes, I've been admitted for fall 2024"

AI: [calls Save Clarifying Question]
    Passes: Question "Have you already been admitted to the university as a transfer student?" 
            Answer "Yes, I've been admitted for fall 2024"
    Returns: Success with counters showing 1 completed, 2 remaining
             Instruction to continue asking questions
    
AI: "Great! Second question: What's your expected start date - when does the fall semester begin?"

User: "August 25th"

AI: [calls Save Clarifying Question]
    Passes: Question "What's your expected start date - when does the fall semester begin?"
            Answer "August 25th"
    Returns: Success with counters showing 2 completed, 1 remaining
             Instruction to continue asking questions
    
AI: "And finally: Do you have any specific questions about the FAFSA itself, or are you mainly looking for information about deadlines and required documents?"

User: "Mainly deadlines and what documents I need"

AI: [calls Save Clarifying Question]
    Passes: Question about FAFSA vs deadlines/documents
            Answer "Mainly deadlines and what documents I need"
    Returns: Success with counters showing 3 completed, 0 remaining
             Draft workflow_phase automatically advanced to "resolution"
             Returns that AI resolution is enabled
             Returns instruction: "Attempt to resolve using submit_ai_resolution with your confidence score and proposed answer"
    
AI: [calls Submit AI Resolution]
    Passes: Confidence score of 85 (out of 100)
            Proposed answer explaining the financial aid process for transfer students
    Returns: Confidence score 85 meets the threshold of 70
             Returns instruction: "Present your resolution to the user and ask if it solved their problem"
    
AI: "I can help explain the financial aid process for transfer students starting in Fall 2024!

**Key Deadlines:**
- Priority FAFSA deadline: June 1, 2024 (though applications accepted on rolling basis after)
- Recommended submission: As soon as possible to maximize aid eligibility
- Aid packages typically sent 2-3 weeks after FAFSA submission

**Required Documents for Transfer Students:**
1. FAFSA completion at studentaid.gov (you'll need your FSA ID)
2. List our school code (001234) on your FAFSA
3. Official transcripts from previous institution(s)
4. Transfer Student Financial Aid Form (available in your student portal)

**Timeline for August 25th Start:**
- Submit FAFSA by mid-June to ensure aid package is ready before fall billing
- Check your student portal weekly for any additional document requests
- Financial aid packages are typically finalized 4-6 weeks before semester start

Does this answer your question, or do you need to speak with our financial aid office directly?"

User: "This is perfect! That's exactly what I needed to know. Thank you!"

AI: [calls Record Resolution Response]
    Passes: Boolean true (user accepted the resolution)
    Returns: Success with generated request number "SR-2026-00456"
             Resolution was accepted so status set to "Closed" (resolved)
             Draft marked as finalized (is_draft = false)
             Request assigned to appropriate team member
             Returns instruction: "Tell the user their question has been answered. Provide the request number..."
    
AI: "Wonderful! I'm glad I could help. Your request has been saved as SR-2026-00456 for our records. If you have any other questions as you go through the financial aid process, feel free to reach out again. Good luck!"
```

### Tool Call Timeline for General Question (No Custom Fields)

```
1. Fetch Service Request Types
   → Returns hierarchical tree of all available types organized by category

2. Show Type Selector
   → Suggests "General Question" type and displays selection UI to user

3. [User selects type via UI - no tool call]
   → System detects type has NO custom form fields, unlocks description tool only

4. Get Draft Status
   → Checks current state and discovers no custom fields exist
   → Returns instruction: "Please describe your issue or request in detail"

5. Update Description
   → Saves detailed explanation about financial aid process
   → Description collected first (no form fields for this type)

6. [AI suggests title in response text - no tool call]
   → AI has description context to suggest an appropriate title

7. Update Title
   → Saves user's confirmed title "Financial aid process guidance for transfer student"
   → Title collected after description

8. Show Priority Selector
   → Displays priority selector widget with High, Medium, Low options

9. [User selects "Medium" priority via UI - no tool call]
   → Priority collected last

10. Submit Service Request
    → Validates all required data (description, title, priority)
    → No custom form fields to validate
    → Advances to clarifying questions phase

11. Save Clarifying Question
    → Records question about admission status (1 of 3)

12. Save Clarifying Question
    → Records question about start date (2 of 3)

13. Save Clarifying Question
    → Records question about specific needs (3 of 3)
    → Automatically advances to resolution phase

14. Submit AI Resolution
    → Proposes comprehensive financial aid guidance with 85% confidence
    → Confidence exceeds threshold, instructed to present to user

15. Record Resolution Response
    → User accepted the resolution
    → Marks request as resolved with status "Closed"
    → Generates request number SR-2026-00456
```

## Technical Implementation Details

## AI Instruction Architecture

The system uses a three-tier approach to guide the AI's behavior, with each tier serving a distinct purpose:

### 1. System Context (SendMessage Job)

**Location**: `SendMessage.php` - `$context` variable in the main execution flow

**Purpose**: Provides global, unchanging rules that apply to ALL interactions regardless of workflow state

**What Belongs Here**:
- Core identity and role ("You are a helpful AI assistant...")
- Universal behavioral rules (markdown formatting, conversational tone, one question at a time)
- General guidelines that never change (knowledge base usage, professional tone)
- Feature-level instructions for service request submission workflow

**Examples**:
- "You MUST format ALL responses using Markdown"
- "Ask ONLY ONE question per message"
- "Never mention technical details like field_ids or JSON"
- "When user wants to submit a request, use `fetch_service_request_types`"

**Key Characteristic**: These instructions are static and included in EVERY request. They establish the AI's personality and fundamental operating principles.

### 2. Tool Descriptions (Tool Constructor)

**Location**: Each tool's `__construct()` method using `->for()` 

**Purpose**: Explains WHEN to use the tool and HOW to use it, but not what to do AFTER

**What Belongs Here**:
- Clear description of the tool's purpose
- When this tool should be called
- What parameters it needs and what they mean
- Behavioral constraints specific to this action (e.g., "ask user first", "don't use conversation history")
- What type of data to pass

**Examples**:
- "Retrieves available service request types. Call this when the user wants to submit a service request"
- "Updates the title of the service request draft. IMPORTANT: You MUST ask the user for the title before calling this tool"
- "Displays a UI widget for complex form fields like selects, radio buttons, dates..."

**Key Characteristic**: Tool descriptions help the AI decide WHETHER to call a tool and HOW to prepare the parameters. They do NOT guide what to do after calling it.

### 3. Tool Response Instructions (Tool Return Value)

**Location**: The JSON response returned by each tool's `__invoke()` method, specifically in an `instruction` field

**Purpose**: Provides CONTEXT-SPECIFIC, DYNAMIC guidance for the immediate next action based on current state

**What Belongs Here**:
- What to do next after this tool executes
- State-specific next steps that depend on the data
- Exact questions to ask the user
- Specific tool to call next with required parameters
- Conditional logic outcomes ("if this, do that")

**Examples**:
- "Ask the user: 'What would you like to title this request?' Then STOP and wait for their response"
- "Call show_field_input with field_id 'abc-123' to display the date input for 'Incident Date'"
- "All required fields are filled. Call submit_service_request to validate and proceed"
- "Confidence too low. Do not show resolution to user. Call finalize_service_request to submit for human review"
- "Present your resolution to the user and ask if it solved their problem"

**Key Characteristic**: These instructions are DYNAMIC and STATE-AWARE. They change based on what data exists, what's missing, what phase the workflow is in, and what the user has provided.

## How the AI Decides What to Do Next

The AI makes decisions using a hierarchical evaluation process:

### Step 1: System Context Awareness
The AI always has access to the global rules from SendMessage. These provide the foundation:
- "I should ask one question at a time"
- "I should use tools to help with service requests"
- "I should be conversational and not technical"

### Step 2: Tool Selection
When the user provides input, the AI evaluates available tool descriptions to determine which action to take:
- User says "I need help with my password" → Tool description says "Call this when user wants to submit a request" → Calls Fetch Service Request Types
- User provides "My student portal login isn't working" → Tool description says "ask user for title before calling" → Asks for title first
- User provides "Cannot access portal" → Tool description says "save immediately without confirmation" → Calls Update Title

### Step 3: Response Instruction Following
After calling a tool, the AI reads the `instruction` field in the response to determine the immediate next action:
- Get Draft Status returns: "Ask the user: 'Student ID' Then STOP..." → AI asks only that question
- Submit Service Request returns: "Ask clarifying question 1 of 3..." → AI asks first clarifying question
- Submit AI Resolution returns: "Present your resolution and ask if it solved their problem" → AI presents resolution

### Step 4: State-Aware Decision Making
The Get Draft Status tool acts as the "traffic controller" by providing precise, context-aware instructions:

```
User provides title → Update Title called → AI calls Get Draft Status
    → Returns: "Ask for description"
    → AI asks for description

User provides description → Update Description called → AI calls Get Draft Status
    → Returns: "Ask the user: 'Student ID'"
    → AI asks for Student ID

User provides Student ID → Update Form Field called → AI calls Get Draft Status
    → Returns: "All required fields filled. Call submit_service_request"
    → AI calls Submit Service Request

Submit Service Request succeeds
    → Returns: "Ask clarifying question 1 of 3"
    → AI asks first question
```

## Design Principles

### Why This Architecture?

1. **Separation of Concerns**
   - System context = personality and universal rules
   - Tool descriptions = selection criteria
   - Tool responses = dynamic next steps

2. **Maintainability**
   - Global behavior changes happen in one place (SendMessage)
   - Tool-specific logic stays with the tool
   - State-dependent decisions are made by the tool that has access to that state

3. **Flexibility**
   - Same tool can give different instructions based on current state
   - Get Draft Status can provide 20+ different instructions depending on what's filled
   - New tools can be added without modifying system context

4. **Predictability**
   - AI doesn't guess what to do next - tools tell it explicitly
   - Reduces hallucination and off-script behavior
   - Forces linear progression through workflow

### Common Pitfalls to Avoid

**❌ DON'T put state-dependent instructions in system context**
```
Bad: "After collecting title, ask for description"
Why: This is state-dependent logic that belongs in tool responses
```

**❌ DON'T put "what to do after" in tool descriptions**
```
Bad: "Updates title. After calling this, ask for description."
Why: What comes after depends on current state, not the tool itself
```

**❌ DON'T put general behavioral rules in tool responses**
```
Bad: Tool returns "Ask one question at a time"
Why: This is a universal rule that belongs in system context
```

**✅ DO use Get Draft Status as the orchestrator**
```
Good: After EVERY save operation, call Get Draft Status to get next instruction
Why: It has access to full draft state and can provide precise guidance
```

**✅ DO make tool responses specific and actionable**
```
Good: "Ask the user: 'What operating system are you using?' Then STOP"
Why: Tells AI exactly what to say and do, no ambiguity
```

**✅ DO use tool descriptions to prevent misuse**
```
Good: "IMPORTANT: You MUST ask the user for the title before calling this tool"
Why: Prevents AI from auto-filling or making assumptions
```

### Tool Loop Message Optimization

To avoid duplicate messages and reduce token usage during tool execution loops, the system uses a custom approach:

**Standard Flow**: The AI sends the full conversation history on each request to maintain context.

**Tool Loop Flow**: When the AI calls tools, the custom handler sends only the two new messages needed for the next iteration:
- The assistant's message containing the tool calls (with text content stripped to avoid duplication)
- The tool result message containing the outputs

This optimization significantly reduces token usage during multi-tool workflows while maintaining the correct context for Azure OpenAI. The implementation uses reflection to temporarily swap the messages array before making the API call, then restores it afterwards.

### Draft Fetching and Tracking

The system uses a pointer-based approach to track which draft is currently active:

**Thread Tracking**: The `PortalAssistantThread` model has a `current_service_request_draft_id` field that points to the currently active draft.

**FindsDraftServiceRequest Trait**: All tools that work with drafts use this trait, which implements:
```
protected function findDraft(): ?ServiceRequest
{
    if ($this->thread->current_service_request_draft_id) {
        return ServiceRequest::find($this->thread->current_service_request_draft_id);
    }
    return null;
}
```

**Type Selection Logic**: When a user selects a type:
1. Check if a draft already exists for that type (by looking for drafts with matching priority.type_id)
2. If exists: Update thread's `current_service_request_draft_id` to point to that draft
3. If not: Create new draft and set thread's pointer to it

This enables type switching while preserving work and maintaining clean separation between different draft attempts.

### Service Request Number Protection

The system's observer pattern prevents any changes to the `service_request_number` field after initial creation. To support retry scenarios where finalization tools may be called multiple times, both the Finalize Service Request and Record Resolution Response tools check if the number already exists before generating a new one. This prevents observer errors while allowing safe retries.

### Database Field Naming

An important implementation detail: the description text is stored in the `close_details` database column, not a `description` column. This affects the conditional tool unlocking logic:
- **For types WITH custom form fields**: The system checks `$draft->close_details` to determine if description has been provided before unlocking submit_service_request
- **For types WITHOUT custom form fields**: The system checks `$draft->close_details` to determine if description has been provided before unlocking the priority selector

### AI Behavior Guidelines

Tool descriptions include explicit instructions to prevent common AI issues:

1. **No Conversation History**: Tools explicitly forbid using information from earlier in the conversation
2. **No Confirmation Loops**: Tools instruct the AI to save immediately without asking for confirmation
3. **No Bold Formatting**: System prompts avoid bold markdown to prevent the AI from mimicking that style
4. **Ask One Question**: Progressive unlocking forces sequential question asking

### Event-Driven UI Updates

Tools that need to display UI widgets emit events rather than returning HTML. For example, when showing the type selector, the tool emits a `PortalAssistantActionRequest` event with the action name and relevant data. The frontend listens for these events via WebSocket or polling and renders the appropriate React/Vue components in the chat interface.

## Configuration

### AI Resolution Settings

The system checks `AiResolutionSettings` to determine AI resolution behavior:
- `is_enabled`: Whether the AI should attempt to resolve requests automatically
- `confidence_threshold`: Minimum confidence score (0-100) required to present a resolution to the user

### Service Request Assignment

After finalization, the service request is automatically assigned to a team member based on the type's configured assignment rules. Each service request type can have an assignment strategy that determines which staff member receives the request.

## Troubleshooting

### Issue: AI Asking Same Question Repeatedly

**Cause**: Tool not unlocking due to field check failure

**Solution**: Verify field names in conditional checks (e.g., `close_details` not `description`)

### Issue: Service Request Number Update Error

**Cause**: Attempting to change the `service_request_number` field after it has been set triggers an observer error.

**Solution**: The finalization tools now check if the number exists before generating a new one, allowing safe retries.

### Issue: AI Auto-Filling Fields from History

**Cause**: The AI uses information from earlier in the conversation instead of asking the user directly.

**Solution**: Tool descriptions explicitly forbid using conversation history and require asking the user for each piece of information.

### Issue: AI Asking "Should I set X to Y?"

**Cause**: The AI interprets tool instructions as requiring explicit confirmation before saving.

**Solution**: Tool descriptions now instruct the AI to "save immediately without asking for confirmation" rather than requesting user approval.

## Future Enhancements

Potential improvements to consider:

1. **Dynamic Question Count**: Allow configurable number of clarifying questions based on service request type
2. **Multi-Language Support**: Translate UI widgets and system prompts
3. **Attachment Handling**: Better support for file uploads during conversation
4. **Resume Draft**: Allow users to continue incomplete drafts from previous sessions
5. **AI Training**: Collect successful resolutions to improve future confidence scores
6. **Analytics**: Track resolution success rates by request type and AI confidence levels
