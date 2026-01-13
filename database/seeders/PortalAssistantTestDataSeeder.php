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

namespace Database\Seeders;

use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Database\Seeders\Concerns\SeedsKnowledgeBaseForPortalAssistant;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Comprehensive seeder for testing Portal Assistant functionality.
 * Run with: php artisan db:seed --class=PortalAssistantTestDataSeeder
 *
 * This seeder:
 * 1. Clears all existing service requests, types, categories, forms
 * 2. Creates 6 categories (2 with nested subcategories) + Testing category
 * 3. Creates 1-5 types per category with realistic forms
 * 4. Creates knowledge base categories aligned with service request categories
 * 5. Seeds 25+ knowledge base articles with AI-verifiable "clues"
 * 6. Enables AI support and triggers knowledge base indexing
 *
 * Each knowledge base article contains a unique verification code (e.g., CLUE-IT-SLOW-7429)
 * that can be used to verify if the AI assistant is actually reading the article content.
 *
 * Field types used in this seeder:
 * - text_input: Simple text fields (no config)
 * - text_area: Multi-line text (no config)
 * - select: Dropdown with options config and optional placeholder
 * - radio: Radio buttons with options config
 * - checkbox: Boolean checkbox (no config)
 * - date: Date picker (no config)
 * - time: Time picker (no config)
 * - number: Numeric input (no config)
 * - email: Email input (no config)
 * - phone: Phone number input (no config)
 * - url: URL input (no config)
 * - signature: Signature capture (no config)
 */
class PortalAssistantTestDataSeeder extends Seeder
{
    use SeedsKnowledgeBaseForPortalAssistant;

    public function run(): void
    {
        $this->clearExistingData();
        $this->createTestData();
        $this->populateStepContent();
        $this->seedKnowledgeBase();
    }

    protected function populateStepContent(): void
    {
        $this->command->info('Populating step content fields...');

        $steps = ServiceRequestFormStep::with(['fields'])->get();

        foreach ($steps as $step) {
            $fieldsData = [];

            foreach ($step->fields as $field) {
                $fieldsData[$field->id] = [
                    'label' => $field->label,
                    'type' => $field->type,
                    'required' => (bool) $field->is_required,
                    'config' => $field->config ?? [],
                ];
            }

            $content = $this->generateStepContent($fieldsData);
            $step->update(['content' => $content]);
        }

        $this->command->info('Step content populated successfully.');
    }

    protected function clearExistingData(): void
    {
        $this->command->info('Clearing existing service request data...');

        // Disable foreign key checks temporarily
        DB::statement("SET session_replication_role = 'replica';");

        // Clear in order of dependencies
        ServiceRequest::withoutGlobalScopes()->forceDelete();
        ServiceRequestFormSubmission::query()->forceDelete();
        ServiceRequestFormField::query()->forceDelete();
        ServiceRequestFormStep::query()->forceDelete();
        ServiceRequestForm::query()->forceDelete();
        ServiceRequestPriority::query()->forceDelete();
        ServiceRequestType::query()->forceDelete();
        ServiceRequestTypeCategory::query()->forceDelete();

        DB::statement("SET session_replication_role = 'origin';");

        $this->command->info('Existing data cleared.');
    }

    protected function createTestData(): void
    {
        $this->command->info('Creating test categories, types, and forms...');

        // Category 1: IT Support (with nested categories)
        $itSupport = $this->createCategory('IT Support', 'heroicon-o-computer-desktop', 1);
        $itHardware = $this->createCategory('Hardware', 'heroicon-o-cpu-chip', 1, $itSupport->id);
        $itSoftware = $this->createCategory('Software', 'heroicon-o-code-bracket', 2, $itSupport->id);
        $itNetwork = $this->createCategory('Network & Connectivity', 'heroicon-o-wifi', 3, $itSupport->id);

        $this->createHardwareTypes($itHardware);
        $this->createSoftwareTypes($itSoftware);
        $this->createNetworkTypes($itNetwork);

        // Category 2: HR Services (with nested categories)
        $hrServices = $this->createCategory('HR Services', 'heroicon-o-user-group', 2);
        $hrBenefits = $this->createCategory('Benefits', 'heroicon-o-gift', 1, $hrServices->id);
        $hrPayroll = $this->createCategory('Payroll', 'heroicon-o-banknotes', 2, $hrServices->id);
        $hrLeave = $this->createCategory('Time Off & Leave', 'heroicon-o-calendar-days', 3, $hrServices->id);

        $this->createBenefitsTypes($hrBenefits);
        $this->createPayrollTypes($hrPayroll);
        $this->createLeaveTypes($hrLeave);

        // Category 3: Facilities Management (no subcategories)
        $facilities = $this->createCategory('Facilities Management', 'heroicon-o-building-office', 3);
        $this->createFacilitiesTypes($facilities);

        // Category 4: Student Services (no subcategories)
        $studentServices = $this->createCategory('Student Services', 'heroicon-o-academic-cap', 4);
        $this->createStudentServicesTypes($studentServices);

        // Category 5: Financial Services (no subcategories)
        $financial = $this->createCategory('Financial Services', 'heroicon-o-currency-dollar', 5);
        $this->createFinancialTypes($financial);

        // Category 6: Testing (for testing all input types)
        $testing = $this->createCategory('Testing', 'heroicon-o-beaker', 6);
        $this->createTestingTypes($testing);

        $this->command->info('Test data created successfully!');
    }

    protected function createCategory(string $name, string $icon, int $sort, ?string $parentId = null): ServiceRequestTypeCategory
    {
        return ServiceRequestTypeCategory::create([
            'id' => Str::uuid()->toString(),
            'name' => $name,
            'sort' => $sort,
            'parent_id' => $parentId,
        ]);
    }

    protected function createType(ServiceRequestTypeCategory $category, string $name, string $description, int $sort): ServiceRequestType
    {
        $type = ServiceRequestType::create([
            'id' => Str::uuid()->toString(),
            'name' => $name,
            'description' => $description,
            'icon' => null,
            'category_id' => $category->id,
            'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
            'sort' => $sort,
        ]);

        // Create standard priorities for each type
        $this->createPriorities($type);

        return $type;
    }

    protected function createPriorities(ServiceRequestType $type): void
    {
        ServiceRequestPriority::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Critical',
            'order' => 1,
            'type_id' => $type->id,
        ]);

        ServiceRequestPriority::create([
            'id' => Str::uuid()->toString(),
            'name' => 'High',
            'order' => 2,
            'type_id' => $type->id,
        ]);

        ServiceRequestPriority::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Medium',
            'order' => 3,
            'type_id' => $type->id,
        ]);

        ServiceRequestPriority::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Low',
            'order' => 4,
            'type_id' => $type->id,
        ]);
    }

    protected function createForm(ServiceRequestType $type, string $name): ServiceRequestForm
    {
        return ServiceRequestForm::create([
            'id' => Str::uuid()->toString(),
            'service_request_type_id' => $type->id,
            'name' => $name,
            'description' => "Form for {$name}",
            'is_wizard' => true,
            'is_authenticated' => true,
            'embed_enabled' => false,
            'recaptcha_enabled' => false,
        ]);
    }

    protected function createStep(ServiceRequestForm $form, string $label, int $sort): ServiceRequestFormStep
    {
        return ServiceRequestFormStep::create([
            'id' => Str::uuid()->toString(),
            'service_request_form_id' => $form->id,
            'label' => $label,
            'sort' => $sort,
        ]);
    }

    protected function generateStepContent(array $fieldsData): array
    {
        $blocks = [];

        foreach ($fieldsData as $fieldId => $fieldData) {
            $blockData = [
                'label' => $fieldData['label'],
                'isRequired' => $fieldData['required'],
            ];

            // Add config if present (options, placeholder, etc.)
            if (! empty($fieldData['config'])) {
                $blockData = array_merge($blockData, $fieldData['config']);
            }

            $blocks[] = [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'type' => $fieldData['type'],
                    'data' => $blockData,
                    'id' => $fieldId,
                ],
            ];
        }

        return [
            'type' => 'doc',
            'content' => $blocks,
        ];
    }

    protected function createField(
        ServiceRequestForm $form,
        ServiceRequestFormStep $step,
        string $label,
        string $type,
        bool $required = false,
        array $config = []
    ): ServiceRequestFormField {
        return ServiceRequestFormField::create([
            'id' => Str::uuid()->toString(),
            'service_request_form_id' => $form->id,
            'service_request_form_step_id' => $step->id,
            'label' => $label,
            'type' => $type,
            'is_required' => $required,
            'config' => $config,
        ]);
    }

    // ========================================================================
    // IT Support - Hardware Types
    // ========================================================================

    protected function createHardwareTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Computer/Laptop Issues
        $computerType = $this->createType($category, 'Computer/Laptop Issue', 'Report problems with your computer or laptop including performance issues, crashes, or hardware failures.', 1);
        $form = $this->createForm($computerType, 'Computer Issue Form');

        $deviceStep = $this->createStep($form, 'Device Information', 1);
        $this->createField($form, $deviceStep, 'Device Type', 'select', true, [
            'options' => [
                'desktop' => 'Desktop Computer',
                'laptop' => 'Laptop',
                'workstation' => 'Workstation',
                'all_in_one' => 'All-in-One',
            ],
            'placeholder' => 'Select device type',
        ]);
        $this->createField($form, $deviceStep, 'Asset Tag Number', 'text_input', true, []);
        $this->createField($form, $deviceStep, 'Operating System', 'select', true, [
            'options' => [
                'windows_11' => 'Windows 11',
                'windows_10' => 'Windows 10',
                'macos' => 'macOS',
                'linux' => 'Linux',
                'other' => 'Other',
            ],
            'placeholder' => 'Select operating system',
        ]);

        $issueStep = $this->createStep($form, 'Issue Details', 2);
        $this->createField($form, $issueStep, 'Problem Category', 'select', true, [
            'options' => [
                'wont_turn_on' => 'Computer won\'t turn on',
                'slow_performance' => 'Slow performance',
                'crashes_freezes' => 'Crashes or freezes',
                'display_issues' => 'Display/monitor issues',
                'keyboard_mouse' => 'Keyboard or mouse not working',
                'overheating' => 'Overheating',
                'strange_noises' => 'Strange noises',
                'other' => 'Other hardware issue',
            ],
            'placeholder' => 'Select the main problem',
        ]);
        $this->createField($form, $issueStep, 'When did this issue start?', 'date', true, []);
        // RADIO field example
        $this->createField($form, $issueStep, 'Is this affecting your ability to work?', 'radio', true, [
            'options' => [
                'completely_blocked' => 'Yes - I cannot work at all',
                'partially_blocked' => 'Partially - Some tasks are affected',
                'minor' => 'No - Minor inconvenience',
            ],
        ]);
        $this->createField($form, $issueStep, 'Additional details', 'text_area', false, []);

        // Type 2: Printer Issues
        $printerType = $this->createType($category, 'Printer Issue', 'Report problems with printers including paper jams, print quality, or connectivity issues.', 2);
        $form = $this->createForm($printerType, 'Printer Issue Form');

        $printerStep = $this->createStep($form, 'Printer Information', 1);
        $this->createField($form, $printerStep, 'Printer Name/Location', 'text_input', true, []);
        $this->createField($form, $printerStep, 'Printer Type', 'select', true, [
            'options' => [
                'laser_bw' => 'Black & White Laser',
                'laser_color' => 'Color Laser',
                'inkjet' => 'Inkjet',
                'multifunction' => 'Multifunction/Copier',
                'label_printer' => 'Label Printer',
            ],
            'placeholder' => 'Select printer type',
        ]);

        $problemStep = $this->createStep($form, 'Problem Details', 2);
        $this->createField($form, $problemStep, 'Issue Type', 'select', true, [
            'options' => [
                'paper_jam' => 'Paper jam',
                'wont_print' => 'Won\'t print at all',
                'print_quality' => 'Poor print quality',
                'offline' => 'Printer showing offline',
                'toner_ink' => 'Toner/ink issues',
                'scanner_issue' => 'Scanner not working',
                'other' => 'Other issue',
            ],
            'placeholder' => 'Select the issue',
        ]);
        $this->createField($form, $problemStep, 'Error message displayed (if any)', 'text_input', false, []);
        $this->createField($form, $problemStep, 'Number of people affected', 'select', false, [
            'options' => [
                'just_me' => 'Just me',
                'my_team' => 'My team/department',
                'multiple_teams' => 'Multiple departments',
                'entire_floor' => 'Entire floor/building',
            ],
            'placeholder' => 'Select scope',
        ]);

        // Type 3: New Equipment Request
        $equipmentType = $this->createType($category, 'New Equipment Request', 'Request new computer equipment, peripherals, or hardware accessories.', 3);
        $form = $this->createForm($equipmentType, 'Equipment Request Form');

        $requestStep = $this->createStep($form, 'Equipment Details', 1);
        $this->createField($form, $requestStep, 'Equipment Type', 'select', true, [
            'options' => [
                'computer' => 'Computer/Laptop',
                'monitor' => 'Monitor',
                'keyboard_mouse' => 'Keyboard/Mouse',
                'headset' => 'Headset/Webcam',
                'docking_station' => 'Docking Station',
                'external_drive' => 'External Storage',
                'other' => 'Other Equipment',
            ],
            'placeholder' => 'Select equipment type',
        ]);
        $this->createField($form, $requestStep, 'Quantity Needed', 'number', true, []);
        $this->createField($form, $requestStep, 'Business Justification', 'text_area', true, []);

        $approvalStep = $this->createStep($form, 'Approval Information', 2);
        $this->createField($form, $approvalStep, 'Cost Center/Budget Code', 'text_input', true, []);
        $this->createField($form, $approvalStep, 'Manager Email', 'email', true, []);
        $this->createField($form, $approvalStep, 'Needed By Date', 'date', false, []);
        // CHECKBOX field example
        $this->createField($form, $approvalStep, 'I confirm this has budget approval', 'checkbox', true, []);
    }

    // ========================================================================
    // IT Support - Software Types
    // ========================================================================

    protected function createSoftwareTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Software Installation Request
        $installType = $this->createType($category, 'Software Installation', 'Request installation of approved software on your computer.', 1);
        $form = $this->createForm($installType, 'Software Installation Form');

        $softwareStep = $this->createStep($form, 'Software Details', 1);
        $this->createField($form, $softwareStep, 'Software Name', 'text_input', true, []);
        $this->createField($form, $softwareStep, 'Software Type', 'select', true, [
            'options' => [
                'productivity' => 'Productivity (Office, PDF, etc.)',
                'communication' => 'Communication (Zoom, Teams, Slack)',
                'development' => 'Development Tools',
                'design' => 'Design/Creative',
                'security' => 'Security Software',
                'specialized' => 'Specialized/Industry Software',
                'other' => 'Other',
            ],
            'placeholder' => 'Select category',
        ]);
        // RADIO field example
        $this->createField($form, $softwareStep, 'License Information', 'radio', true, [
            'options' => [
                'have_license' => 'I have a license key',
                'need_license' => 'Need license purchase',
                'free_software' => 'Free/Open source software',
                'not_sure' => 'Not sure',
            ],
        ]);
        $this->createField($form, $softwareStep, 'License Key (if available)', 'text_input', false, []);
        // URL field example
        $this->createField($form, $softwareStep, 'Software Download URL (if known)', 'url', false, []);

        $deviceStep = $this->createStep($form, 'Target Device', 2);
        $this->createField($form, $deviceStep, 'Computer Asset Tag', 'text_input', true, []);
        $this->createField($form, $deviceStep, 'Business Justification', 'text_area', true, []);

        // Type 2: Application Error/Bug
        $bugType = $this->createType($category, 'Application Error', 'Report errors, bugs, or crashes in software applications.', 2);
        $form = $this->createForm($bugType, 'Application Error Form');

        $appStep = $this->createStep($form, 'Application Information', 1);
        $this->createField($form, $appStep, 'Application Name', 'text_input', true, []);
        $this->createField($form, $appStep, 'Application Version (if known)', 'text_input', false, []);
        $this->createField($form, $appStep, 'Operating System', 'select', true, [
            'options' => [
                'windows_11' => 'Windows 11',
                'windows_10' => 'Windows 10',
                'macos' => 'macOS',
                'web_browser' => 'Web Browser',
                'mobile_ios' => 'iOS',
                'mobile_android' => 'Android',
            ],
            'placeholder' => 'Select platform',
        ]);

        $errorStep = $this->createStep($form, 'Error Details', 2);
        $this->createField($form, $errorStep, 'Error Type', 'select', true, [
            'options' => [
                'crash' => 'Application crashes/closes unexpectedly',
                'freeze' => 'Application freezes/hangs',
                'error_message' => 'Error message displayed',
                'feature_broken' => 'Feature not working correctly',
                'slow' => 'Very slow performance',
                'login_issue' => 'Cannot log in',
                'other' => 'Other issue',
            ],
            'placeholder' => 'Select error type',
        ]);
        $this->createField($form, $errorStep, 'Error Message (if any)', 'text_area', false, []);
        $this->createField($form, $errorStep, 'Steps to Reproduce', 'text_area', true, []);
        // RADIO field example
        $this->createField($form, $errorStep, 'Can you reproduce this issue?', 'radio', true, [
            'options' => [
                'always' => 'Yes, every time',
                'sometimes' => 'Sometimes',
                'once' => 'It only happened once',
            ],
        ]);

        // Type 3: Password Reset
        $passwordType = $this->createType($category, 'Password Reset', 'Request a password reset for your accounts.', 3);
        $form = $this->createForm($passwordType, 'Password Reset Form');

        $accountStep = $this->createStep($form, 'Account Information', 1);
        $this->createField($form, $accountStep, 'Account/System', 'select', true, [
            'options' => [
                'network' => 'Network/Windows Login',
                'email' => 'Email (Outlook/Gmail)',
                'hr_system' => 'HR System',
                'erp' => 'ERP/Financial System',
                'crm' => 'CRM System',
                'vpn' => 'VPN',
                'other' => 'Other System',
            ],
            'placeholder' => 'Select the system',
        ]);
        $this->createField($form, $accountStep, 'Username', 'text_input', true, []);
        $this->createField($form, $accountStep, 'Other System Name (if Other selected)', 'text_input', false, []);

        $verifyStep = $this->createStep($form, 'Verification', 2);
        $this->createField($form, $verifyStep, 'Employee ID', 'text_input', true, []);
        $this->createField($form, $verifyStep, 'Phone Number for Verification', 'phone', true, []);

        // Type 4: Access Request
        $accessType = $this->createType($category, 'Access Request', 'Request access to systems, folders, or applications.', 4);
        $form = $this->createForm($accessType, 'Access Request Form');

        $accessStep = $this->createStep($form, 'Access Details', 1);
        $this->createField($form, $accessStep, 'Resource Type', 'select', true, [
            'options' => [
                'application' => 'Application/Software',
                'shared_folder' => 'Shared Folder/Drive',
                'database' => 'Database',
                'website' => 'Website/Portal',
                'distribution_list' => 'Email Distribution List',
                'teams_channel' => 'Teams/Slack Channel',
            ],
            'placeholder' => 'Select resource type',
        ]);
        $this->createField($form, $accessStep, 'Resource Name', 'text_input', true, []);
        // RADIO field example
        $this->createField($form, $accessStep, 'Access Level Needed', 'radio', true, [
            'options' => [
                'read_only' => 'Read Only',
                'read_write' => 'Read/Write',
                'admin' => 'Administrator',
            ],
        ]);

        $justificationStep = $this->createStep($form, 'Justification', 2);
        $this->createField($form, $justificationStep, 'Business Reason', 'text_area', true, []);
        $this->createField($form, $justificationStep, 'Manager Approval Email', 'email', true, []);
        // RADIO field example
        $this->createField($form, $justificationStep, 'Temporary or Permanent?', 'radio', true, [
            'options' => [
                'permanent' => 'Permanent',
                'temporary' => 'Temporary',
            ],
        ]);
        $this->createField($form, $justificationStep, 'End Date (if temporary)', 'date', false, []);
        // CHECKBOX field example
        $this->createField($form, $justificationStep, 'I acknowledge the data security policy', 'checkbox', true, []);
    }

    // ========================================================================
    // IT Support - Network Types
    // ========================================================================

    protected function createNetworkTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: WiFi/Internet Issues
        $wifiType = $this->createType($category, 'WiFi/Internet Issue', 'Report problems with wireless or internet connectivity.', 1);
        $form = $this->createForm($wifiType, 'WiFi Issue Form');

        $locationStep = $this->createStep($form, 'Location Information', 1);
        $this->createField($form, $locationStep, 'Building', 'text_input', true, []);
        $this->createField($form, $locationStep, 'Floor/Area', 'text_input', true, []);
        $this->createField($form, $locationStep, 'Room Number (if applicable)', 'text_input', false, []);

        $issueStep = $this->createStep($form, 'Connectivity Issue', 2);
        $this->createField($form, $issueStep, 'Problem Type', 'select', true, [
            'options' => [
                'no_connection' => 'Cannot connect to WiFi',
                'slow' => 'Slow internet speed',
                'intermittent' => 'Connection drops frequently',
                'no_internet' => 'Connected but no internet',
                'specific_site' => 'Cannot access specific websites',
            ],
            'placeholder' => 'Select the problem',
        ]);
        // RADIO field example
        $this->createField($form, $issueStep, 'Which network are you trying to connect to?', 'radio', true, [
            'options' => [
                'corporate' => 'Corporate WiFi',
                'guest' => 'Guest WiFi',
                'wired' => 'Wired/Ethernet',
            ],
        ]);
        $this->createField($form, $issueStep, 'Number of devices affected', 'select', true, [
            'options' => [
                'one' => 'Just my device',
                'multiple' => 'Multiple devices',
                'all_area' => 'Everyone in the area',
            ],
            'placeholder' => 'Select scope',
        ]);
        $this->createField($form, $issueStep, 'Device MAC Address (if known)', 'text_input', false, []);

        // Type 2: VPN Issues
        $vpnType = $this->createType($category, 'VPN Issue', 'Report problems connecting to or using the VPN.', 2);
        $form = $this->createForm($vpnType, 'VPN Issue Form');

        $vpnStep = $this->createStep($form, 'VPN Details', 1);
        $this->createField($form, $vpnStep, 'VPN Client', 'select', true, [
            'options' => [
                'cisco_anyconnect' => 'Cisco AnyConnect',
                'global_protect' => 'GlobalProtect',
                'pulse_secure' => 'Pulse Secure',
                'forticlient' => 'FortiClient',
                'other' => 'Other',
            ],
            'placeholder' => 'Select VPN client',
        ]);
        $this->createField($form, $vpnStep, 'Problem Type', 'select', true, [
            'options' => [
                'cannot_connect' => 'Cannot connect at all',
                'authentication' => 'Authentication failure',
                'disconnects' => 'Keeps disconnecting',
                'slow' => 'Very slow when connected',
                'cannot_access' => 'Connected but cannot access resources',
            ],
            'placeholder' => 'Select the issue',
        ]);
        $this->createField($form, $vpnStep, 'Error Message', 'text_area', false, []);

        $envStep = $this->createStep($form, 'Environment', 2);
        $this->createField($form, $envStep, 'Where are you connecting from?', 'select', true, [
            'options' => [
                'home' => 'Home',
                'public_wifi' => 'Public WiFi (coffee shop, airport)',
                'hotel' => 'Hotel',
                'client_site' => 'Client location',
                'other' => 'Other',
            ],
            'placeholder' => 'Select location',
        ]);
        $this->createField($form, $envStep, 'Internet Service Provider (if known)', 'text_input', false, []);
    }

    // ========================================================================
    // HR Services - Benefits Types
    // ========================================================================

    protected function createBenefitsTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Benefits Enrollment
        $enrollType = $this->createType($category, 'Benefits Enrollment', 'Enroll in or make changes to your employee benefits.', 1);
        $form = $this->createForm($enrollType, 'Benefits Enrollment Form');

        $requestStep = $this->createStep($form, 'Enrollment Request', 1);
        $this->createField($form, $requestStep, 'Request Type', 'select', true, [
            'options' => [
                'new_enrollment' => 'New Enrollment',
                'change' => 'Change Existing Benefits',
                'add_dependent' => 'Add Dependent',
                'remove_dependent' => 'Remove Dependent',
                'cancel' => 'Cancel Benefits',
            ],
            'placeholder' => 'Select request type',
        ]);
        $this->createField($form, $requestStep, 'Benefit Type', 'select', true, [
            'options' => [
                'health' => 'Health Insurance',
                'dental' => 'Dental Insurance',
                'vision' => 'Vision Insurance',
                'life' => 'Life Insurance',
                'disability' => 'Disability Insurance',
                'fsa' => 'Flexible Spending Account (FSA)',
                'hsa' => 'Health Savings Account (HSA)',
                '401k' => '401(k) Retirement',
            ],
            'placeholder' => 'Select benefit',
        ]);
        $this->createField($form, $requestStep, 'Qualifying Event', 'select', true, [
            'options' => [
                'new_hire' => 'New Hire',
                'open_enrollment' => 'Open Enrollment',
                'marriage' => 'Marriage',
                'divorce' => 'Divorce',
                'birth' => 'Birth/Adoption of Child',
                'loss_coverage' => 'Loss of Other Coverage',
                'other' => 'Other Qualifying Event',
            ],
            'placeholder' => 'Select event',
        ]);
        $this->createField($form, $requestStep, 'Event Date', 'date', true, []);

        // Type 2: Benefits Question
        $questionType = $this->createType($category, 'Benefits Question', 'Ask questions about your benefits coverage or options.', 2);
        $form = $this->createForm($questionType, 'Benefits Question Form');

        $questionStep = $this->createStep($form, 'Your Question', 1);
        $this->createField($form, $questionStep, 'Topic', 'select', true, [
            'options' => [
                'coverage' => 'Coverage Details',
                'claims' => 'Claims/Reimbursement',
                'providers' => 'Finding Providers',
                'costs' => 'Costs/Premiums',
                'eligibility' => 'Eligibility',
                'other' => 'Other',
            ],
            'placeholder' => 'Select topic',
        ]);
        $this->createField($form, $questionStep, 'Which benefit plan?', 'select', false, [
            'options' => [
                'health' => 'Health Insurance',
                'dental' => 'Dental',
                'vision' => 'Vision',
                'retirement' => '401(k)/Retirement',
                'fsa_hsa' => 'FSA/HSA',
                'other' => 'Other',
            ],
            'placeholder' => 'Select plan',
        ]);
        $this->createField($form, $questionStep, 'Your Question', 'text_area', true, []);
    }

    // ========================================================================
    // HR Services - Payroll Types
    // ========================================================================

    protected function createPayrollTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Paycheck Issue
        $paycheckType = $this->createType($category, 'Paycheck Issue', 'Report problems with your paycheck including incorrect amounts or missing pay.', 1);
        $form = $this->createForm($paycheckType, 'Paycheck Issue Form');

        $issueStep = $this->createStep($form, 'Issue Details', 1);
        $this->createField($form, $issueStep, 'Pay Period End Date', 'date', true, []);
        $this->createField($form, $issueStep, 'Issue Type', 'select', true, [
            'options' => [
                'missing_pay' => 'Missing paycheck',
                'incorrect_amount' => 'Incorrect amount',
                'missing_hours' => 'Missing hours/overtime',
                'wrong_deductions' => 'Incorrect deductions',
                'tax_withholding' => 'Tax withholding issue',
                'direct_deposit' => 'Direct deposit not received',
                'other' => 'Other issue',
            ],
            'placeholder' => 'Select issue type',
        ]);
        $this->createField($form, $issueStep, 'Expected Amount', 'text_input', false, []);
        $this->createField($form, $issueStep, 'Received Amount', 'text_input', false, []);
        $this->createField($form, $issueStep, 'Description of Issue', 'text_area', true, []);

        // Type 2: Direct Deposit Change
        $ddType = $this->createType($category, 'Direct Deposit Change', 'Update your direct deposit bank account information.', 2);
        $form = $this->createForm($ddType, 'Direct Deposit Form');

        $bankStep = $this->createStep($form, 'Bank Information', 1);
        $this->createField($form, $bankStep, 'Change Type', 'select', true, [
            'options' => [
                'add' => 'Add new account',
                'change' => 'Change existing account',
                'remove' => 'Remove account',
                'update_split' => 'Update split percentages',
            ],
            'placeholder' => 'Select change type',
        ]);
        $this->createField($form, $bankStep, 'Bank Name', 'text_input', true, []);
        // RADIO field example
        $this->createField($form, $bankStep, 'Account Type', 'radio', true, [
            'options' => [
                'checking' => 'Checking',
                'savings' => 'Savings',
            ],
        ]);
        $this->createField($form, $bankStep, 'Routing Number', 'text_input', true, []);
        $this->createField($form, $bankStep, 'Account Number', 'text_input', true, []);
        $this->createField($form, $bankStep, 'Deposit Percentage', 'select', true, [
            'options' => [
                '100' => '100%',
                '75' => '75%',
                '50' => '50%',
                '25' => '25%',
                'flat' => 'Flat Amount',
            ],
            'placeholder' => 'Select percentage',
        ]);
        // CHECKBOX field
        $this->createField($form, $bankStep, 'I verify this account information is correct', 'checkbox', true, []);

        // Type 3: Tax Form Request
        $taxType = $this->createType($category, 'Tax Form Request', 'Request copies of tax documents (W-2, 1099, etc.).', 3);
        $form = $this->createForm($taxType, 'Tax Form Request');

        $formStep = $this->createStep($form, 'Form Details', 1);
        $this->createField($form, $formStep, 'Form Type', 'select', true, [
            'options' => [
                'w2' => 'W-2',
                '1099' => '1099',
                'w4' => 'W-4 (Update Withholding)',
                'state_form' => 'State Tax Form',
            ],
            'placeholder' => 'Select form',
        ]);
        $this->createField($form, $formStep, 'Tax Year', 'select', true, [
            'options' => [
                '2025' => '2025',
                '2024' => '2024',
                '2023' => '2023',
                '2022' => '2022',
            ],
            'placeholder' => 'Select year',
        ]);
        // RADIO field example
        $this->createField($form, $formStep, 'Delivery Method', 'radio', true, [
            'options' => [
                'electronic' => 'Electronic (Email/Portal)',
                'mail' => 'Mail to Address on File',
                'pickup' => 'Pick Up in HR Office',
            ],
        ]);
    }

    // ========================================================================
    // HR Services - Leave Types
    // ========================================================================

    protected function createLeaveTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Leave of Absence Request
        $leaveType = $this->createType($category, 'Leave of Absence', 'Request an extended leave of absence (FMLA, personal, medical).', 1);
        $form = $this->createForm($leaveType, 'Leave of Absence Form');

        $leaveStep = $this->createStep($form, 'Leave Details', 1);
        $this->createField($form, $leaveStep, 'Leave Type', 'select', true, [
            'options' => [
                'fmla' => 'FMLA (Family Medical Leave)',
                'medical' => 'Medical Leave',
                'personal' => 'Personal Leave',
                'parental' => 'Parental Leave',
                'bereavement' => 'Bereavement',
                'military' => 'Military Leave',
                'sabbatical' => 'Sabbatical',
            ],
            'placeholder' => 'Select leave type',
        ]);
        $this->createField($form, $leaveStep, 'Start Date', 'date', true, []);
        $this->createField($form, $leaveStep, 'Expected Return Date', 'date', true, []);
        // RADIO field example
        $this->createField($form, $leaveStep, 'Is this intermittent leave?', 'radio', true, [
            'options' => [
                'no' => 'No - Continuous leave',
                'yes' => 'Yes - Intermittent/reduced schedule',
            ],
        ]);

        $detailStep = $this->createStep($form, 'Additional Information', 2);
        $this->createField($form, $detailStep, 'Reason for Leave', 'text_area', true, []);
        $this->createField($form, $detailStep, 'Contact Phone During Leave', 'phone', true, []);
        $this->createField($form, $detailStep, 'Contact Email During Leave', 'email', true, []);
        // CHECKBOX field
        $this->createField($form, $detailStep, 'I have discussed this leave with my manager', 'checkbox', true, []);

        // Type 2: PTO Balance Inquiry
        $ptoType = $this->createType($category, 'PTO Balance Inquiry', 'Ask questions about your PTO balance or accrual.', 2);
        $form = $this->createForm($ptoType, 'PTO Inquiry Form');

        $inquiryStep = $this->createStep($form, 'Inquiry Details', 1);
        $this->createField($form, $inquiryStep, 'Question Type', 'select', true, [
            'options' => [
                'balance' => 'Current balance question',
                'accrual' => 'Accrual rate question',
                'discrepancy' => 'Balance discrepancy',
                'carryover' => 'Carryover policy',
                'payout' => 'PTO payout question',
            ],
            'placeholder' => 'Select question type',
        ]);
        // RADIO field example
        $this->createField($form, $inquiryStep, 'PTO Type', 'radio', true, [
            'options' => [
                'vacation' => 'Vacation',
                'sick' => 'Sick Time',
                'personal' => 'Personal Days',
                'floating' => 'Floating Holidays',
            ],
        ]);
        $this->createField($form, $inquiryStep, 'Your Question', 'text_area', true, []);
    }

    // ========================================================================
    // Facilities Management Types
    // ========================================================================

    protected function createFacilitiesTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Maintenance Request
        $maintType = $this->createType($category, 'Maintenance Request', 'Report facility issues requiring repair or maintenance.', 1);
        $form = $this->createForm($maintType, 'Maintenance Request Form');

        $locationStep = $this->createStep($form, 'Location', 1);
        $this->createField($form, $locationStep, 'Building', 'text_input', true, []);
        $this->createField($form, $locationStep, 'Floor', 'text_input', true, []);
        $this->createField($form, $locationStep, 'Room/Area', 'text_input', true, []);

        $issueStep = $this->createStep($form, 'Issue Details', 2);
        $this->createField($form, $issueStep, 'Issue Category', 'select', true, [
            'options' => [
                'plumbing' => 'Plumbing (leak, clog, toilet)',
                'electrical' => 'Electrical (lights, outlets)',
                'hvac' => 'HVAC (heating, cooling, ventilation)',
                'doors_windows' => 'Doors/Windows',
                'flooring' => 'Flooring/Carpet',
                'ceiling' => 'Ceiling/Roof leak',
                'furniture' => 'Furniture repair',
                'pest' => 'Pest control',
                'other' => 'Other',
            ],
            'placeholder' => 'Select category',
        ]);
        $this->createField($form, $issueStep, 'Description of Problem', 'text_area', true, []);
        // RADIO field example
        $this->createField($form, $issueStep, 'Is this a safety hazard?', 'radio', true, [
            'options' => [
                'yes' => 'Yes - Immediate safety concern',
                'potential' => 'Potentially - Could become hazardous',
                'no' => 'No - Not a safety issue',
            ],
        ]);
        $this->createField($form, $issueStep, 'Best time for maintenance visit', 'select', false, [
            'options' => [
                'anytime' => 'Anytime',
                'morning' => 'Morning (8am-12pm)',
                'afternoon' => 'Afternoon (12pm-5pm)',
                'after_hours' => 'After business hours',
            ],
            'placeholder' => 'Select preference',
        ]);

        // Type 2: Room/Space Booking
        $bookingType = $this->createType($category, 'Room Booking Request', 'Request to book a conference room or event space.', 2);
        $form = $this->createForm($bookingType, 'Room Booking Form');

        $eventStep = $this->createStep($form, 'Event Details', 1);
        $this->createField($form, $eventStep, 'Event Name/Purpose', 'text_input', true, []);
        $this->createField($form, $eventStep, 'Date', 'date', true, []);
        $this->createField($form, $eventStep, 'Start Time', 'time', true, []);
        $this->createField($form, $eventStep, 'End Time', 'time', true, []);
        $this->createField($form, $eventStep, 'Number of Attendees', 'number', true, []);

        $roomStep = $this->createStep($form, 'Room Requirements', 2);
        $this->createField($form, $roomStep, 'Preferred Room (if any)', 'text_input', false, []);
        // RADIO field example
        $this->createField($form, $roomStep, 'Room Setup', 'radio', true, [
            'options' => [
                'conference' => 'Conference/Boardroom style',
                'classroom' => 'Classroom style',
                'theater' => 'Theater style',
                'u_shape' => 'U-Shape',
            ],
        ]);
        $this->createField($form, $roomStep, 'Equipment Needed', 'select', false, [
            'options' => [
                'none' => 'None',
                'projector' => 'Projector/Screen',
                'video_conf' => 'Video conferencing',
                'whiteboard' => 'Whiteboard',
                'phone' => 'Conference phone',
            ],
            'placeholder' => 'Select equipment',
        ]);
        // RADIO field example
        $this->createField($form, $roomStep, 'Catering Required?', 'radio', true, [
            'options' => [
                'no' => 'No',
                'yes_internal' => 'Yes - Internal catering',
                'yes_external' => 'Yes - External catering (need approval)',
            ],
        ]);

        // Type 3: Parking Request
        $parkingType = $this->createType($category, 'Parking Request', 'Request parking access, visitor parking, or report parking issues.', 3);
        $form = $this->createForm($parkingType, 'Parking Request Form');

        $requestStep = $this->createStep($form, 'Request Details', 1);
        $this->createField($form, $requestStep, 'Request Type', 'select', true, [
            'options' => [
                'new_permit' => 'New parking permit',
                'visitor' => 'Visitor parking pass',
                'replacement' => 'Replacement permit',
                'upgrade' => 'Upgrade parking location',
                'issue' => 'Report parking issue',
            ],
            'placeholder' => 'Select request type',
        ]);
        $this->createField($form, $requestStep, 'Vehicle Make/Model', 'text_input', true, []);
        $this->createField($form, $requestStep, 'License Plate Number', 'text_input', true, []);
        $this->createField($form, $requestStep, 'License Plate State', 'text_input', true, []);

        $visitorStep = $this->createStep($form, 'Visitor Information (if applicable)', 2);
        $this->createField($form, $visitorStep, 'Visitor Name', 'text_input', false, []);
        $this->createField($form, $visitorStep, 'Visit Date', 'date', false, []);
        $this->createField($form, $visitorStep, 'Duration of Visit', 'select', false, [
            'options' => [
                'half_day' => 'Half day',
                'full_day' => 'Full day',
                'multiple_days' => 'Multiple days',
                'ongoing' => 'Ongoing (contractor)',
            ],
            'placeholder' => 'Select duration',
        ]);

        // Type 4: Cleaning Request
        $cleaningType = $this->createType($category, 'Cleaning Request', 'Request special cleaning or report cleanliness issues.', 4);
        $form = $this->createForm($cleaningType, 'Cleaning Request Form');

        $cleaningStep = $this->createStep($form, 'Cleaning Details', 1);
        $this->createField($form, $cleaningStep, 'Building', 'text_input', true, []);
        $this->createField($form, $cleaningStep, 'Floor/Area', 'text_input', true, []);
        $this->createField($form, $cleaningStep, 'Specific Location', 'text_input', true, []);
        $this->createField($form, $cleaningStep, 'Request Type', 'select', true, [
            'options' => [
                'spill' => 'Spill cleanup',
                'restroom' => 'Restroom needs attention',
                'trash' => 'Trash/recycling full',
                'deep_clean' => 'Deep cleaning needed',
                'special_event' => 'Pre/post event cleaning',
                'other' => 'Other',
            ],
            'placeholder' => 'Select type',
        ]);
        $this->createField($form, $cleaningStep, 'Description', 'text_area', true, []);
        // RADIO field example
        $this->createField($form, $cleaningStep, 'Urgency', 'radio', true, [
            'options' => [
                'urgent' => 'Urgent - Safety/health concern',
                'today' => 'Today please',
                'this_week' => 'This week',
            ],
        ]);

        // Type 5: Key/Access Card Request
        $keyType = $this->createType($category, 'Key/Access Card Request', 'Request building keys or access card programming.', 5);
        $form = $this->createForm($keyType, 'Key Access Form');

        $keyStep = $this->createStep($form, 'Access Request', 1);
        $this->createField($form, $keyStep, 'Request Type', 'select', true, [
            'options' => [
                'new_card' => 'New access card',
                'lost_card' => 'Replace lost card',
                'damaged_card' => 'Replace damaged card',
                'add_access' => 'Add building/room access',
                'remove_access' => 'Remove access',
                'physical_key' => 'Physical key request',
            ],
            'placeholder' => 'Select type',
        ]);
        $this->createField($form, $keyStep, 'Access Needed For', 'text_area', true, []);
        $this->createField($form, $keyStep, 'Reason for Request', 'text_area', true, []);
        $this->createField($form, $keyStep, 'Manager Approval Email', 'email', true, []);
        // CHECKBOX field
        $this->createField($form, $keyStep, 'I agree to return keys/card upon termination', 'checkbox', true, []);
    }

    // ========================================================================
    // Student Services Types
    // ========================================================================

    protected function createStudentServicesTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Enrollment Issue
        $enrollType = $this->createType($category, 'Enrollment Issue', 'Report problems with course enrollment or registration.', 1);
        $form = $this->createForm($enrollType, 'Enrollment Issue Form');

        $studentStep = $this->createStep($form, 'Student Information', 1);
        $this->createField($form, $studentStep, 'Student ID', 'text_input', true, []);
        $this->createField($form, $studentStep, 'Program of Study', 'text_input', true, []);
        $this->createField($form, $studentStep, 'Academic Term', 'select', true, [
            'options' => [
                'fall_2025' => 'Fall 2025',
                'spring_2026' => 'Spring 2026',
                'summer_2026' => 'Summer 2026',
            ],
            'placeholder' => 'Select term',
        ]);

        $issueStep = $this->createStep($form, 'Issue Details', 2);
        $this->createField($form, $issueStep, 'Issue Type', 'select', true, [
            'options' => [
                'cannot_register' => 'Cannot register for classes',
                'prerequisite' => 'Prerequisite error/override needed',
                'course_full' => 'Needed course is full',
                'schedule_conflict' => 'Schedule conflict',
                'add_drop' => 'Add/Drop issue',
                'waitlist' => 'Waitlist question',
                'other' => 'Other enrollment issue',
            ],
            'placeholder' => 'Select issue',
        ]);
        $this->createField($form, $issueStep, 'Course Number(s)', 'text_input', true, []);
        $this->createField($form, $issueStep, 'Description of Issue', 'text_area', true, []);
        // RADIO field example
        $this->createField($form, $issueStep, 'Have you spoken with your advisor?', 'radio', true, [
            'options' => [
                'yes_approved' => 'Yes - Advisor approved',
                'yes_referred' => 'Yes - Referred me here',
                'no' => 'No',
            ],
        ]);

        // Type 2: Financial Aid Question
        $finaidType = $this->createType($category, 'Financial Aid Question', 'Ask questions about financial aid, scholarships, or student loans.', 2);
        $form = $this->createForm($finaidType, 'Financial Aid Form');

        $questionStep = $this->createStep($form, 'Question Details', 1);
        $this->createField($form, $questionStep, 'Student ID', 'text_input', true, []);
        $this->createField($form, $questionStep, 'Topic', 'select', true, [
            'options' => [
                'fafsa' => 'FAFSA questions',
                'award_letter' => 'Award letter questions',
                'disbursement' => 'Disbursement timing',
                'scholarship' => 'Scholarships',
                'loans' => 'Student loans',
                'work_study' => 'Work study',
                'verification' => 'Verification documents',
                'appeal' => 'SAP Appeal',
                'other' => 'Other',
            ],
            'placeholder' => 'Select topic',
        ]);
        // RADIO field example
        $this->createField($form, $questionStep, 'Academic Year', 'radio', true, [
            'options' => [
                '2025_2026' => '2025-2026',
                '2024_2025' => '2024-2025',
            ],
        ]);
        $this->createField($form, $questionStep, 'Your Question', 'text_area', true, []);

        // Type 3: Transcript Request
        $transcriptType = $this->createType($category, 'Transcript Request', 'Request official or unofficial transcripts.', 3);
        $form = $this->createForm($transcriptType, 'Transcript Request Form');

        $transcriptStep = $this->createStep($form, 'Transcript Details', 1);
        $this->createField($form, $transcriptStep, 'Student ID', 'text_input', true, []);
        // RADIO field example
        $this->createField($form, $transcriptStep, 'Transcript Type', 'radio', true, [
            'options' => [
                'official_electronic' => 'Official - Electronic',
                'official_paper' => 'Official - Paper',
                'unofficial' => 'Unofficial',
            ],
        ]);
        $this->createField($form, $transcriptStep, 'Number of Copies', 'number', true, []);
        $this->createField($form, $transcriptStep, 'Delivery Speed', 'select', true, [
            'options' => [
                'standard' => 'Standard (3-5 business days)',
                'rush' => 'Rush (1-2 business days)',
                'pickup' => 'Office pickup',
            ],
            'placeholder' => 'Select speed',
        ]);

        $recipientStep = $this->createStep($form, 'Recipient Information', 2);
        $this->createField($form, $recipientStep, 'Recipient Name/Organization', 'text_input', true, []);
        $this->createField($form, $recipientStep, 'Recipient Email (for electronic)', 'email', false, []);
        $this->createField($form, $recipientStep, 'Mailing Address (for paper)', 'text_area', false, []);

        // Type 4: Disability Accommodations
        $accommodationType = $this->createType($category, 'Disability Accommodations', 'Request disability accommodations or accessibility services.', 4);
        $form = $this->createForm($accommodationType, 'Accommodations Form');

        $requestStep = $this->createStep($form, 'Request Information', 1);
        $this->createField($form, $requestStep, 'Student ID', 'text_input', true, []);
        $this->createField($form, $requestStep, 'Request Type', 'select', true, [
            'options' => [
                'initial' => 'Initial accommodation request',
                'update' => 'Update existing accommodations',
                'semester_letter' => 'Semester accommodation letters',
                'testing' => 'Testing accommodations',
                'housing' => 'Housing accommodations',
                'question' => 'General question',
            ],
            'placeholder' => 'Select type',
        ]);
        $this->createField($form, $requestStep, 'Description of Need', 'text_area', true, []);
        // RADIO field example
        $this->createField($form, $requestStep, 'Do you have documentation?', 'radio', true, [
            'options' => [
                'yes_will_send' => 'Yes - Will send separately',
                'yes_on_file' => 'Yes - Already on file',
                'no' => 'No - Need guidance',
            ],
        ]);
        $this->createField($form, $requestStep, 'Best Contact Phone', 'phone', true, []);

        // Type 5: Academic Advising Appointment
        $advisingType = $this->createType($category, 'Advising Appointment', 'Schedule an appointment with an academic advisor.', 5);
        $form = $this->createForm($advisingType, 'Advising Appointment Form');

        $appointmentStep = $this->createStep($form, 'Appointment Request', 1);
        $this->createField($form, $appointmentStep, 'Student ID', 'text_input', true, []);
        $this->createField($form, $appointmentStep, 'Major/Program', 'text_input', true, []);
        $this->createField($form, $appointmentStep, 'Appointment Type', 'select', true, [
            'options' => [
                'registration' => 'Registration/Course planning',
                'degree_audit' => 'Degree audit review',
                'major_change' => 'Change of major',
                'academic_standing' => 'Academic standing discussion',
                'graduation' => 'Graduation check',
                'general' => 'General advising',
            ],
            'placeholder' => 'Select type',
        ]);
        // RADIO field example
        $this->createField($form, $appointmentStep, 'Meeting Format Preference', 'radio', true, [
            'options' => [
                'in_person' => 'In-person',
                'virtual' => 'Virtual (Zoom/Teams)',
                'either' => 'Either is fine',
            ],
        ]);
        $this->createField($form, $appointmentStep, 'Topics to Discuss', 'text_area', true, []);
        $this->createField($form, $appointmentStep, 'Preferred Date/Time', 'text_input', false, []);
    }

    // ========================================================================
    // Financial Services Types
    // ========================================================================

    protected function createFinancialTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: Expense Reimbursement
        $expenseType = $this->createType($category, 'Expense Reimbursement', 'Submit business expenses for reimbursement.', 1);
        $form = $this->createForm($expenseType, 'Expense Reimbursement Form');

        $expenseStep = $this->createStep($form, 'Expense Details', 1);
        $this->createField($form, $expenseStep, 'Expense Category', 'select', true, [
            'options' => [
                'travel' => 'Travel (airfare, hotel, car)',
                'meals' => 'Meals & Entertainment',
                'supplies' => 'Office Supplies',
                'equipment' => 'Equipment Purchase',
                'conference' => 'Conference/Training',
                'mileage' => 'Mileage Reimbursement',
                'other' => 'Other',
            ],
            'placeholder' => 'Select category',
        ]);
        $this->createField($form, $expenseStep, 'Total Amount', 'text_input', true, []);
        $this->createField($form, $expenseStep, 'Expense Date', 'date', true, []);
        $this->createField($form, $expenseStep, 'Business Purpose', 'text_area', true, []);
        // URL field - link to booking confirmation
        $this->createField($form, $expenseStep, 'Booking Confirmation URL (if applicable)', 'url', false, []);

        $receiptsStep = $this->createStep($form, 'Documentation', 2);
        $this->createField($form, $receiptsStep, 'Cost Center/Budget Code', 'text_input', true, []);
        $this->createField($form, $receiptsStep, 'Manager Approval Email', 'email', true, []);
        $this->createField($form, $receiptsStep, 'Receipt Notes', 'text_area', false, []);
        // CHECKBOX field
        $this->createField($form, $receiptsStep, 'I certify these expenses are accurate and business-related', 'checkbox', true, []);

        // Type 2: Invoice Payment
        $invoiceType = $this->createType($category, 'Invoice Payment Request', 'Submit vendor invoices for payment.', 2);
        $form = $this->createForm($invoiceType, 'Invoice Payment Form');

        $vendorStep = $this->createStep($form, 'Vendor Information', 1);
        $this->createField($form, $vendorStep, 'Vendor Name', 'text_input', true, []);
        // RADIO field example
        $this->createField($form, $vendorStep, 'Is this a new vendor?', 'radio', true, [
            'options' => [
                'no' => 'No - Existing vendor',
                'yes' => 'Yes - New vendor (W-9 required)',
            ],
        ]);
        $this->createField($form, $vendorStep, 'Invoice Number', 'text_input', true, []);
        $this->createField($form, $vendorStep, 'Invoice Amount', 'text_input', true, []);
        $this->createField($form, $vendorStep, 'Invoice Date', 'date', true, []);
        $this->createField($form, $vendorStep, 'Payment Due Date', 'date', true, []);
        // URL field - vendor website
        $this->createField($form, $vendorStep, 'Vendor Website', 'url', false, []);

        $approvalStep = $this->createStep($form, 'Approval Information', 2);
        $this->createField($form, $approvalStep, 'Cost Center/GL Code', 'text_input', true, []);
        $this->createField($form, $approvalStep, 'Purchase Order Number (if applicable)', 'text_input', false, []);
        $this->createField($form, $approvalStep, 'Description of Goods/Services', 'text_area', true, []);
        $this->createField($form, $approvalStep, 'Approver Email', 'email', true, []);

        // Type 3: Budget Question
        $budgetType = $this->createType($category, 'Budget Inquiry', 'Ask questions about budgets, spending, or financial reports.', 3);
        $form = $this->createForm($budgetType, 'Budget Inquiry Form');

        $inquiryStep = $this->createStep($form, 'Inquiry Details', 1);
        $this->createField($form, $inquiryStep, 'Department', 'text_input', true, []);
        $this->createField($form, $inquiryStep, 'Cost Center(s)', 'text_input', true, []);
        $this->createField($form, $inquiryStep, 'Inquiry Type', 'select', true, [
            'options' => [
                'balance' => 'Budget balance question',
                'transfer' => 'Budget transfer request',
                'report' => 'Financial report request',
                'variance' => 'Variance explanation',
                'forecast' => 'Forecasting question',
                'other' => 'Other',
            ],
            'placeholder' => 'Select type',
        ]);
        // RADIO field example
        $this->createField($form, $inquiryStep, 'Fiscal Year', 'radio', true, [
            'options' => [
                'fy2026' => 'FY 2026',
                'fy2025' => 'FY 2025',
            ],
        ]);
        $this->createField($form, $inquiryStep, 'Your Question', 'text_area', true, []);

        // Type 4: Purchasing Card Issue
        $pcardType = $this->createType($category, 'P-Card Issue', 'Report problems with your purchasing card or request a new card.', 4);
        $form = $this->createForm($pcardType, 'P-Card Issue Form');

        $cardStep = $this->createStep($form, 'Card Details', 1);
        $this->createField($form, $cardStep, 'Issue Type', 'select', true, [
            'options' => [
                'new_card' => 'Request new P-Card',
                'lost_stolen' => 'Lost/Stolen card',
                'declined' => 'Transaction declined',
                'limit_increase' => 'Request limit increase',
                'dispute' => 'Dispute a charge',
                'reconciliation' => 'Reconciliation help',
                'close' => 'Close card',
            ],
            'placeholder' => 'Select issue',
        ]);
        $this->createField($form, $cardStep, 'Last 4 digits of card (if applicable)', 'text_input', false, []);

        $detailStep = $this->createStep($form, 'Details', 2);
        $this->createField($form, $detailStep, 'Description', 'text_area', true, []);
        $this->createField($form, $detailStep, 'Transaction Date (if applicable)', 'date', false, []);
        $this->createField($form, $detailStep, 'Transaction Amount (if applicable)', 'text_input', false, []);
        $this->createField($form, $detailStep, 'Manager Email', 'email', true, []);
        // CHECKBOX for lost/stolen
        $this->createField($form, $detailStep, 'If lost/stolen: I confirm I have not made unauthorized purchases', 'checkbox', false, []);
    }

    // ========================================================================
    // Testing Types - All Input Types
    // ========================================================================

    protected function createTestingTypes(ServiceRequestTypeCategory $category): void
    {
        // Type 1: All Input Types Test
        $testType = $this->createType(
            $category,
            'All Input Types Test',
            'A test form containing every available input type for validation and testing purposes.',
            1
        );
        $form = $this->createForm($testType, 'All Input Types Form');

        // Step 1: Text-based inputs
        $textStep = $this->createStep($form, 'Text Inputs', 1);
        $this->createField($form, $textStep, 'Simple Text Input', 'text_input', true, []);
        $this->createField($form, $textStep, 'Multi-line Text Area', 'text_area', true, []);
        $this->createField($form, $textStep, 'Email Address', 'email', true, []);
        $this->createField($form, $textStep, 'Phone Number', 'phone', true, []);
        $this->createField($form, $textStep, 'Website URL', 'url', false, []);
        $this->createField($form, $textStep, 'Numeric Value', 'number', true, []);

        // Step 2: Selection inputs
        $selectionStep = $this->createStep($form, 'Selection Inputs', 2);
        $this->createField($form, $selectionStep, 'Dropdown Select', 'select', true, [
            'options' => [
                'option_a' => 'Option A - First Choice',
                'option_b' => 'Option B - Second Choice',
                'option_c' => 'Option C - Third Choice',
                'option_d' => 'Option D - Fourth Choice',
            ],
            'placeholder' => 'Select an option',
        ]);
        $this->createField($form, $selectionStep, 'Radio Button Selection', 'radio', true, [
            'options' => [
                'radio_1' => 'Radio Option 1',
                'radio_2' => 'Radio Option 2',
                'radio_3' => 'Radio Option 3',
            ],
        ]);
        $this->createField($form, $selectionStep, 'Checkbox Agreement', 'checkbox', true, []);

        // Step 3: Date, Time, and Special inputs
        $specialStep = $this->createStep($form, 'Date, Time & Special', 3);
        $this->createField($form, $specialStep, 'Date Picker', 'date', true, []);
        $this->createField($form, $specialStep, 'Time Picker', 'time', true, []);
        $this->createField($form, $specialStep, 'Signature Capture', 'signature', true, []);
    }
}
