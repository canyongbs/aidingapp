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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace Database\Seeders\Concerns;

use AidingApp\Ai\Jobs\PrepareKnowledgeBaseVectorStore;
use AidingApp\Division\Models\Division;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Support\Str;

/**
 * Trait for seeding knowledge base articles for Portal Assistant testing.
 * Articles are designed to match service request categories and contain
 * AI-verifiable "clues" to confirm the AI is reading the content.
 */
trait SeedsKnowledgeBaseForPortalAssistant
{
    protected function seedKnowledgeBase(): void
    {
        $this->command->info('Seeding knowledge base categories and articles...');

        // Ensure required statuses and qualities exist
        $publishedStatus = KnowledgeBaseStatus::firstOrCreate(['name' => 'Published']);
        $goodQuality = KnowledgeBaseQuality::firstOrCreate(['name' => 'Good']);
        $division = Division::first();

        // Create categories aligned with service request categories
        $categories = $this->createKnowledgeBaseCategories();

        // Create articles for each category
        $this->createITSupportArticles($categories['it_support'], $publishedStatus, $goodQuality, $division);
        $this->createHRArticles($categories['hr_services'], $publishedStatus, $goodQuality, $division);
        $this->createFacilitiesArticles($categories['facilities'], $publishedStatus, $goodQuality, $division);
        $this->createStudentServicesArticles($categories['student_services'], $publishedStatus, $goodQuality, $division);
        $this->createFinancialArticles($categories['financial'], $publishedStatus, $goodQuality, $division);

        // Enable AI support and trigger indexing
        $this->enableAISupportAndIndex();

        $this->command->info('Knowledge base seeding completed!');
    }

    protected function createKnowledgeBaseCategories(): array
    {
        return [
            'it_support' => KnowledgeBaseCategory::create([
                'name' => 'IT Support & Technology',
                'slug' => 'it-support-technology',
                'description' => 'Technical support articles for hardware, software, and network issues.',
                'icon' => null,
            ]),
            'hr_services' => KnowledgeBaseCategory::create([
                'name' => 'Human Resources',
                'slug' => 'human-resources',
                'description' => 'HR policies, benefits information, and employee services.',
                'icon' => null,
            ]),
            'facilities' => KnowledgeBaseCategory::create([
                'name' => 'Facilities & Building Services',
                'slug' => 'facilities-building-services',
                'description' => 'Building maintenance, room booking, and facilities information.',
                'icon' => null,
            ]),
            'student_services' => KnowledgeBaseCategory::create([
                'name' => 'Student Services & Academic',
                'slug' => 'student-services-academic',
                'description' => 'Academic support, enrollment, and student resources.',
                'icon' => null,
            ]),
            'financial' => KnowledgeBaseCategory::create([
                'name' => 'Financial Services',
                'slug' => 'financial-services',
                'description' => 'Expense reimbursement, budgets, and financial procedures.',
                'icon' => null,
            ]),
        ];
    }

    protected function createArticle(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division,
        string $title,
        string $content,
        string $clue
    ): KnowledgeBaseItem {
        // Add the clue prominently in the content
        $fullContent = "**AI Verification Code: {$clue}**\n\n{$content}\n\n---\n*Reference: {$clue}*";

        $article = KnowledgeBaseItem::create([
            'title' => $title,
            'article_details' => $this->formatAsTiptap($fullContent),
            'notes' => "Clue: {$clue}",
            'public' => true,
            'quality_id' => $quality->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
        ]);

        if ($division) {
            $article->division()->attach($division->id);
        }

        return $article;
    }

    protected function formatAsTiptap(string $markdown): array
    {
        // Convert markdown to TipTap JSON format
        $paragraphs = array_filter(explode("\n\n", $markdown));
        $content = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) {
                continue;
            }

            // Handle headings
            if (str_starts_with($paragraph, '## ')) {
                $content[] = [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [['type' => 'text', 'text' => substr($paragraph, 3)]],
                ];
            } elseif (str_starts_with($paragraph, '### ')) {
                $content[] = [
                    'type' => 'heading',
                    'attrs' => ['level' => 3],
                    'content' => [['type' => 'text', 'text' => substr($paragraph, 4)]],
                ];
            } elseif (str_starts_with($paragraph, '- ')) {
                // Handle bullet list
                $items = array_filter(explode("\n", $paragraph));
                $listItems = [];
                foreach ($items as $item) {
                    $itemText = ltrim($item, '- ');
                    $listItems[] = [
                        'type' => 'listItem',
                        'content' => [
                            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $itemText]]],
                        ],
                    ];
                }
                $content[] = ['type' => 'bulletList', 'content' => $listItems];
            } elseif (str_starts_with($paragraph, '1. ')) {
                // Handle numbered list
                $items = array_filter(explode("\n", $paragraph));
                $listItems = [];
                foreach ($items as $item) {
                    $itemText = preg_replace('/^\d+\.\s*/', '', $item);
                    $listItems[] = [
                        'type' => 'listItem',
                        'content' => [
                            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $itemText]]],
                        ],
                    ];
                }
                $content[] = ['type' => 'orderedList', 'content' => $listItems];
            } else {
                // Regular paragraph - strip bold markers for simplicity
                $cleanText = str_replace(['**', '*'], '', $paragraph);
                $content[] = [
                    'type' => 'paragraph',
                    'content' => [['type' => 'text', 'text' => $cleanText]],
                ];
            }
        }

        return ['type' => 'doc', 'content' => $content];
    }

    protected function createITSupportArticles(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division
    ): void {
        // Computer Issues
        $this->createArticle($category, $status, $quality, $division,
            'Troubleshooting Slow Computer Performance',
            "## Common Causes of Slow Computers\n\nIf your computer is running slowly, there are several things to check:\n\n### Quick Fixes\n\n1. Restart your computer - this clears temporary files\n2. Check for Windows updates that need to be installed\n3. Close unnecessary browser tabs and applications\n4. Check available disk space (need at least 10% free)\n\n### When to Contact IT\n\nContact IT Support if:\n- Computer takes more than 5 minutes to boot\n- Applications freeze frequently\n- You see error messages about memory\n- The slowness started after installing new software\n\nThe standard response time for performance issues is 24-48 hours unless it's preventing you from working entirely.",
            'CLUE-IT-SLOW-7429'
        );

        $this->createArticle($category, $status, $quality, $division,
            'Computer Won\'t Turn On - What To Do',
            "## First Steps When Computer Won\'t Power On\n\nBefore contacting IT, try these steps:\n\n### Check Power Connection\n\n1. Verify the power cable is firmly connected to both the computer and outlet\n2. Try a different power outlet\n3. Check if the power strip is turned on (if using one)\n4. Look for any indicator lights on the computer\n\n### For Laptops\n\n- Make sure the battery is charged\n- Try removing the battery and using only AC power\n- Hold the power button for 30 seconds, then try again\n\n### Emergency Procedures\n\nIf you need to work urgently, IT can provide a loaner laptop within 2 hours during business hours. The emergency IT line is extension 4357 (HELP).",
            'CLUE-IT-POWER-8831'
        );

        // Printer Issues
        $this->createArticle($category, $status, $quality, $division,
            'Resolving Common Printer Problems',
            "## Printer Troubleshooting Guide\n\n### Paper Jams\n\nTo clear a paper jam:\n\n1. Turn off the printer\n2. Open all access panels\n3. Gently pull paper in the direction it normally travels\n4. Check for small torn pieces\n5. Close panels and restart\n\n### Printer Shows Offline\n\n1. Check the printer's network connection\n2. Restart the print spooler service\n3. Remove and re-add the printer\n\n### Print Quality Issues\n\n- Run the printer's cleaning cycle\n- Check toner/ink levels\n- Ensure you're using the correct paper type\n\nThe main office printer (HP LaserJet 500 in Room 201) is maintained every Tuesday morning. Report recurring issues to facilities.",
            'CLUE-IT-PRINT-3356'
        );

        // Software Installation
        $this->createArticle($category, $status, $quality, $division,
            'Software Installation Request Process',
            "## How to Request Software Installation\n\nAll software installations must be approved by IT for security and licensing compliance.\n\n### Approved Software List\n\nThe following can be installed within 24 hours:\n- Microsoft Office Suite\n- Adobe Acrobat Reader\n- Zoom, Teams, Slack\n- Chrome, Firefox browsers\n\n### Requesting New Software\n\n1. Submit a service request through the portal\n2. Include the software name and business justification\n3. IT will verify licensing requirements\n4. Typical approval takes 3-5 business days\n\n### License Information\n\nIf you have a personal license key, include it in your request. For departmental purchases, contact your budget manager first. The software budget code for IT purchases is CC-IT-2025-SW.",
            'CLUE-IT-SOFTWARE-2294'
        );

        // Password Reset
        $this->createArticle($category, $status, $quality, $division,
            'Password Reset and Account Recovery',
            "## Password Reset Procedures\n\n### Self-Service Password Reset\n\nYou can reset your network password at: password.company.internal\n\nRequirements:\n- Must have registered recovery email or phone\n- Answer security questions correctly\n- New password must be at least 12 characters\n- Cannot reuse last 10 passwords\n\n### Locked Accounts\n\nAccounts lock after 5 failed login attempts. Wait 30 minutes or contact IT.\n\n### Emergency Access\n\nFor urgent password resets:\n- Call the IT Help Desk: ext. 4357\n- Provide your employee ID and manager's name\n- Verification takes 5-10 minutes\n\nThe security policy requiring quarterly password changes was updated in January 2025 - passwords now expire every 90 days instead of 60.",
            'CLUE-IT-PASSWORD-5567'
        );

        // VPN Issues
        $this->createArticle($category, $status, $quality, $division,
            'VPN Connection Troubleshooting',
            "## VPN Setup and Troubleshooting\n\n### Supported VPN Clients\n\nWe use Cisco AnyConnect for remote access. Download from: vpn.company.internal\n\n### Connection Issues\n\n1. Check your internet connection first\n2. Try disconnecting and reconnecting\n3. Restart the VPN client\n4. Check if your antivirus is blocking the connection\n\n### Common Error Messages\n\n- \"Authentication Failed\" - Verify username/password, ensure no caps lock\n- \"Connection Attempt Failed\" - Try a different network, hotel WiFi often blocks VPN\n- \"Certificate Error\" - VPN client may need updating\n\n### Split Tunneling\n\nSplit tunneling is enabled by default, meaning only company traffic goes through VPN. For full tunnel mode (required for some applications), contact IT. The VPN server maintenance window is Sundays 2-4 AM.",
            'CLUE-IT-VPN-9943'
        );

        // WiFi Issues
        $this->createArticle($category, $status, $quality, $division,
            'WiFi Network Information and Troubleshooting',
            "## Campus WiFi Networks\n\n### Available Networks\n\n- **CorpWiFi** - For employees, requires network login\n- **GuestWiFi** - For visitors, daily password posted in reception\n- **IoT-Devices** - For printers and smart devices only\n\n### Connecting to CorpWiFi\n\n1. Select CorpWiFi from available networks\n2. Enter your network username and password\n3. Accept the security certificate\n\n### Dead Zones and Weak Signal Areas\n\nKnown areas with limited coverage:\n- Basement storage rooms\n- Stairwell B (between floors 2-3)\n- Parking garage level 2\n\nA WiFi expansion project is planned for Q2 2026. Report new dead zones to help us prioritize coverage improvements.",
            'CLUE-IT-WIFI-1187'
        );

        // Equipment Request
        $this->createArticle($category, $status, $quality, $division,
            'Requesting New Computer Equipment',
            "## Equipment Request Process\n\n### Standard Equipment\n\nNew employees receive:\n- Laptop or desktop (based on role)\n- Monitor (27\" standard, dual monitors for developers)\n- Keyboard, mouse, and headset\n\n### Requesting Additional Equipment\n\n1. Submit a service request with justification\n2. Include budget code for departmental charges\n3. Manager approval required for items over \$500\n\n### Equipment Refresh Cycle\n\n- Laptops: Replaced every 4 years\n- Desktops: Replaced every 5 years\n- Monitors: Replaced when defective\n\n### Loaner Equipment\n\nFor temporary needs, IT maintains a pool of loaner laptops. Reserve at least 48 hours in advance. The equipment checkout form requires your asset tag number, which can be found on the silver sticker on the bottom of your device.",
            'CLUE-IT-EQUIP-4421'
        );

        // Application Errors
        $this->createArticle($category, $status, $quality, $division,
            'Reporting Application Errors and Crashes',
            "## How to Report Application Problems\n\n### Before Reporting\n\nTry these steps first:\n1. Close and reopen the application\n2. Restart your computer\n3. Check if the issue happens in other applications\n4. Note any error messages exactly as shown\n\n### Information to Include\n\nWhen submitting a service request for application errors:\n- Application name and version\n- Exact error message (screenshot helpful)\n- Steps to reproduce the problem\n- When the issue started\n- Whether it affects just you or others\n\n### Common Application Issues\n\n- **Application freezes**: Often resolved by clearing cache or temp files\n- **Crashes on startup**: May indicate corrupted installation\n- **Feature not working**: Could be permissions or configuration issue\n\nThe IT team maintains a known issues list at help.company.internal/known-issues. Check there first to see if your issue is already being addressed.",
            'CLUE-IT-APPERR-6678'
        );

        // Access Requests
        $this->createArticle($category, $status, $quality, $division,
            'Requesting Access to Systems and Resources',
            "## System Access Request Process\n\n### Types of Access\n\n- **Application Access**: Software systems like ERP, CRM, HR systems\n- **Shared Folders**: Department drives and shared storage\n- **Database Access**: Read or write access to databases\n- **Distribution Lists**: Email groups and Teams channels\n\n### How to Request Access\n\n1. Submit a service request through the portal\n2. Specify the resource name and access level needed\n3. Provide business justification\n4. Include manager approval (email confirmation acceptable)\n\n### Access Levels\n\n- **Read Only**: View but not modify\n- **Read/Write**: View and modify content\n- **Administrator**: Full control including user management\n\n### Approval Timeline\n\n- Standard requests: 2-3 business days\n- Sensitive systems: 5-7 business days (requires security review)\n- Temporary access: Can be expedited with manager approval\n\nAll access is reviewed quarterly. Unused access may be revoked automatically after 90 days of inactivity.",
            'CLUE-IT-ACCESS-3392'
        );

        // Computer Overheating
        $this->createArticle($category, $status, $quality, $division,
            'Computer Overheating Issues',
            "## Signs of Computer Overheating\n\n### Warning Signs\n\n- Fan running constantly at high speed\n- Computer feels hot to touch\n- Unexpected shutdowns\n- Performance slowdown during intensive tasks\n- Blue screen errors mentioning temperature\n\n### Immediate Actions\n\n1. Save your work immediately\n2. Close resource-intensive applications\n3. Ensure vents are not blocked\n4. Move to a cooler location if possible\n5. If laptop, use on hard flat surface (not bed or lap)\n\n### Prevention Tips\n\n- Keep vents clear of dust and debris\n- Use a laptop cooling pad for intensive work\n- Don't block air vents with papers or objects\n- Report persistent overheating to IT\n\n### When to Contact IT\n\nContact IT if overheating persists after basic troubleshooting. The thermal paste may need replacement or internal fans may need cleaning. Laptops over 3 years old are more prone to overheating issues.",
            'CLUE-IT-OVERHEAT-7745'
        );

        // Display and Monitor Issues
        $this->createArticle($category, $status, $quality, $division,
            'Display and Monitor Troubleshooting',
            "## Common Display Problems\n\n### No Display/Black Screen\n\n1. Check monitor is powered on\n2. Verify cable connections (both ends)\n3. Try a different cable if available\n4. Test with a different monitor\n5. Check if laptop lid is fully open\n\n### Flickering or Distorted Display\n\n- Update graphics drivers\n- Check cable for damage\n- Try different refresh rate in display settings\n- External interference from nearby electronics\n\n### Resolution and Scaling Issues\n\n- Right-click desktop > Display Settings\n- Select recommended resolution\n- Adjust scaling for text size preference\n\n### Multiple Monitor Setup\n\n- Windows + P to switch display modes\n- Extend: Different content on each screen\n- Duplicate: Same content on both screens\n- Second screen only: Main display off\n\nFor persistent display issues, IT can provide a replacement monitor within 24 hours. Submit a service request with your building and room number.",
            'CLUE-IT-DISPLAY-8823'
        );

        // Keyboard and Mouse Issues
        $this->createArticle($category, $status, $quality, $division,
            'Keyboard and Mouse Problems',
            "## Troubleshooting Input Devices\n\n### Keyboard Not Working\n\n1. Check USB connection or wireless receiver\n2. Try a different USB port\n3. Replace batteries if wireless\n4. Check if Num Lock/Caps Lock accidentally on\n5. Restart computer\n\n### Mouse Issues\n\n- **Cursor jumping**: Clean mouse sensor and mousepad\n- **Buttons not responding**: Check for debris under buttons\n- **Wireless mouse laggy**: Move closer to receiver, check batteries\n\n### Laptop Keyboard/Trackpad\n\n- Check if external keyboard is overriding internal\n- Verify trackpad isn't disabled (Fn + F7 on most laptops)\n- Clean with compressed air for stuck keys\n\n### Requesting Replacement\n\nIT stocks standard keyboards and mice for immediate replacement. Visit IT Support in Room 150 or submit a service request for delivery. Ergonomic keyboards require manager approval and 1-week lead time.",
            'CLUE-IT-INPUT-4456'
        );

        // Operating System Updates
        $this->createArticle($category, $status, $quality, $division,
            'Operating System Updates and Patches',
            "## Windows Update Policy\n\n### Automatic Updates\n\nCompany computers receive updates automatically:\n- Security patches: Within 48 hours of release\n- Feature updates: Quarterly during maintenance windows\n- Driver updates: As needed for compatibility\n\n### Update Schedule\n\n- Updates install automatically overnight\n- Computer must be on but can be locked\n- Restart required after major updates\n\n### Troubleshooting Failed Updates\n\n1. Check available disk space (need 10GB free)\n2. Restart and try again\n3. Check Windows Update troubleshooter\n4. Contact IT if updates fail repeatedly\n\n### Deferring Updates\n\nCritical security updates cannot be deferred. For feature updates during busy periods, contact IT for temporary deferral (max 30 days). The next major Windows update is scheduled for March 2026.",
            'CLUE-IT-UPDATES-2267'
        );
    }

    protected function createHRArticles(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division
    ): void {
        // Benefits
        $this->createArticle($category, $status, $quality, $division,
            'Employee Benefits Overview',
            "## Benefits Package Summary\n\n### Health Insurance\n\nWe offer three health plan options:\n- **Basic Plan** - Lower premium, higher deductible (\$2,500)\n- **Standard Plan** - Balanced coverage, \$1,000 deductible\n- **Premium Plan** - Comprehensive coverage, \$500 deductible\n\nOpen enrollment is November 1-15 annually.\n\n### Dental and Vision\n\n- Dental covers 100% preventive, 80% basic, 50% major\n- Vision includes annual exam and \$200 frame allowance\n\n### Retirement\n\n- 401(k) with 4% company match\n- Vesting schedule: 2 years\n- Enrollment is immediate for new hires\n\nThe benefits hotline is 1-800-555-BENE (2363), available Monday-Friday 8 AM - 6 PM EST.",
            'CLUE-HR-BENEFITS-6632'
        );

        // PTO
        $this->createArticle($category, $status, $quality, $division,
            'Paid Time Off (PTO) Policy',
            "## PTO Accrual and Usage\n\n### Accrual Rates\n\n- Years 0-2: 15 days annually\n- Years 3-5: 20 days annually\n- Years 6+: 25 days annually\n\nPTO accrues each pay period and is available immediately.\n\n### Requesting Time Off\n\n1. Submit request in the HR portal at least 2 weeks in advance\n2. Manager approval required\n3. Blackout dates apply during quarter-end closing\n\n### Carryover Policy\n\n- Maximum 5 days can roll over to next year\n- Unused days above 5 are forfeited on December 31\n- Payout of unused PTO only upon termination\n\nThe company observes 10 paid holidays plus 2 floating holidays. Check the HR calendar for the current year's holiday schedule.",
            'CLUE-HR-PTO-8876'
        );

        // Payroll
        $this->createArticle($category, $status, $quality, $division,
            'Payroll Information and Direct Deposit',
            "## Payroll Schedule\n\nPay periods are bi-weekly, with pay dates every other Friday.\n\n### Direct Deposit\n\n- Set up through the HR portal\n- Changes must be submitted by Tuesday to take effect the following pay period\n- Can split between up to 3 accounts\n\n### Pay Stub Access\n\nPay stubs are available in the HR portal:\n- Current and past 2 years accessible online\n- Historical pay stubs (older than 2 years) available upon request\n\n### Common Payroll Issues\n\n- Missing hours: Contact your timekeeper within 2 days of pay date\n- Tax withholding changes: Submit new W-4 through HR portal\n- Address changes: Update in HR portal under \"Personal Information\"\n\nThe payroll department can be reached at payroll@company.com. Response time is typically within 1 business day.",
            'CLUE-HR-PAYROLL-3349'
        );

        // Leave of Absence
        $this->createArticle($category, $status, $quality, $division,
            'Leave of Absence Procedures',
            "## Types of Leave\n\n### FMLA (Family Medical Leave)\n\n- Eligible after 12 months of employment\n- Up to 12 weeks unpaid, job-protected leave\n- For serious health conditions, new child, or family member care\n\n### Parental Leave\n\n- 12 weeks paid for primary caregiver\n- 4 weeks paid for secondary caregiver\n- Must be taken within 6 months of birth/adoption\n\n### Personal Leave\n\n- Up to 30 days unpaid\n- Requires director-level approval\n- Must exhaust PTO first\n\n### Bereavement\n\n- 5 days for immediate family\n- 3 days for extended family\n- Paid leave, no PTO deduction\n\nLeave requests should be submitted at least 30 days in advance when foreseeable. Contact HR for emergency situations.",
            'CLUE-HR-LEAVE-5521'
        );

        // Tax Forms
        $this->createArticle($category, $status, $quality, $division,
            'Tax Documents and W-2 Information',
            "## Annual Tax Documents\n\n### W-2 Distribution\n\n- W-2 forms are available by January 31\n- Electronic W-2s available in HR portal\n- Paper copies mailed to address on file\n\n### Accessing Previous Years\n\nPast W-2s available in HR portal for 7 years. For older documents, submit a request to HR.\n\n### Updating Tax Withholding\n\n1. Download W-4 from IRS website or HR portal\n2. Complete the form with new information\n3. Submit to HR via portal or in person\n4. Changes take effect within 2 pay periods\n\n### State Tax Forms\n\n- Multi-state employees may receive multiple state forms\n- Remote workers: Tax based on work location, not residence\n\nFor tax questions, we recommend consulting a tax professional. HR cannot provide tax advice. The company EIN is 12-3456789.",
            'CLUE-HR-TAX-7744'
        );

        // Benefits Enrollment
        $this->createArticle($category, $status, $quality, $division,
            'How to Enroll in Benefits',
            "## Benefits Enrollment Process\n\n### When to Enroll\n\n- **New Hires**: Within 30 days of start date\n- **Open Enrollment**: November 1-15 annually\n- **Qualifying Events**: Within 30 days of event\n\n### Qualifying Life Events\n\n- Marriage or divorce\n- Birth or adoption of child\n- Loss of other coverage\n- Change in employment status\n\n### Enrollment Steps\n\n1. Log in to HR portal\n2. Navigate to Benefits > Enroll\n3. Review available plans and costs\n4. Select your coverage levels\n5. Add dependents if applicable\n6. Confirm elections\n\n### Required Documentation\n\nFor dependents, you must provide:\n- Marriage certificate for spouse\n- Birth certificate for children\n- Adoption papers if applicable\n\nBenefits become effective the 1st of the month following enrollment. For new hires starting mid-month, coverage begins the 1st of the following month.",
            'CLUE-HR-ENROLL-8891'
        );

        // Paycheck Issues
        $this->createArticle($category, $status, $quality, $division,
            'Resolving Paycheck Problems',
            "## Common Paycheck Issues\n\n### Missing or Incorrect Pay\n\nIf your paycheck is incorrect:\n1. Review your pay stub for details\n2. Compare to your timesheet entries\n3. Check for any deductions you may have forgotten\n4. Submit a service request with specifics\n\n### Types of Paycheck Issues\n\n- **Missing hours**: Contact your timekeeper first\n- **Incorrect rate**: May be a job code issue\n- **Wrong deductions**: Check benefit elections\n- **Direct deposit failed**: Verify bank information\n\n### Resolution Timeline\n\n- Emergency corrections: Same day for hardship cases\n- Standard corrections: Next pay period\n- Retroactive adjustments: 2-3 pay periods\n\n### Preventing Issues\n\n- Submit timesheets by Monday 5 PM\n- Review pay stubs each pay period\n- Report discrepancies within 30 days\n\nThe payroll correction request form is available in the HR portal under Forms > Payroll. Include all supporting documentation.",
            'CLUE-HR-PAYCHK-4456'
        );

        // Direct Deposit Changes
        $this->createArticle($category, $status, $quality, $division,
            'Changing Direct Deposit Information',
            "## Updating Bank Account Information\n\n### How to Change Direct Deposit\n\n1. Log in to HR portal\n2. Go to Pay > Direct Deposit\n3. Add new account or edit existing\n4. Enter routing and account numbers\n5. Specify deposit amount or percentage\n\n### Important Deadlines\n\n- Changes must be submitted by Tuesday 5 PM\n- Takes effect the following pay period\n- Allow one full pay cycle for verification\n\n### Splitting Direct Deposit\n\nYou can split your pay across up to 3 accounts:\n- Percentage-based splits\n- Fixed dollar amounts\n- Remainder to primary account\n\n### Verification Process\n\nFor security, direct deposit changes require:\n- Verification email sent to your work email\n- 24-hour waiting period before activation\n- Voided check or bank letter for new accounts\n\nKeep your old account open until you verify the new deposit is successful. The first deposit to a new account may be delayed 1-2 days.",
            'CLUE-HR-DEPOSIT-7723'
        );

        // PTO Balance
        $this->createArticle($category, $status, $quality, $division,
            'Checking Your PTO Balance',
            "## PTO Balance Information\n\n### Where to Find Your Balance\n\n- **HR Portal**: Dashboard shows current balance\n- **Pay Stub**: Listed under \"Leave Balances\"\n- **Mobile App**: HR mobile app shows real-time balance\n\n### Understanding Your Balance\n\n- **Available**: Can be used now\n- **Pending**: Approved but not yet taken\n- **Accrued YTD**: Total earned this year\n- **Used YTD**: Total used this year\n\n### Accrual Calculation\n\nPTO accrues each pay period:\n- 0-2 years: 5.77 hours per pay period (15 days/year)\n- 3-5 years: 7.69 hours per pay period (20 days/year)\n- 6+ years: 9.62 hours per pay period (25 days/year)\n\n### Balance Discrepancies\n\nIf your balance seems incorrect:\n1. Check recent time-off requests\n2. Verify approved vs. pending requests\n3. Review any corrections or adjustments\n4. Contact HR if still unclear\n\nBalances update within 24 hours of timesheet approval. Year-end carryover is processed by January 15.",
            'CLUE-HR-PTOBAL-3318'
        );
    }

    protected function createFacilitiesArticles(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division
    ): void {
        // Maintenance
        $this->createArticle($category, $status, $quality, $division,
            'Reporting Facility Maintenance Issues',
            "## How to Report Maintenance Problems\n\n### Emergency Issues\n\nFor emergencies (flooding, fire, power outage), call Security immediately: ext. 9999\n\n### Standard Maintenance Requests\n\n1. Submit through the service request portal\n2. Include building, floor, and room number\n3. Describe the issue in detail\n4. Attach photos if possible\n\n### Response Times\n\n- Safety hazards: Within 2 hours\n- Essential services (HVAC, plumbing): Same day\n- General maintenance: 3-5 business days\n- Cosmetic issues: As scheduled\n\n### Common Issues\n\n- Light bulb replacement: Submit request, no self-replacement\n- Temperature issues: Thermostat adjustments by facilities only\n- Pest sightings: Report immediately for same-day treatment\n\nThe facilities team works Monday-Friday 7 AM - 6 PM. After-hours emergencies are handled by Security.",
            'CLUE-FAC-MAINT-2238'
        );

        // Room Booking
        $this->createArticle($category, $status, $quality, $division,
            'Conference Room Booking Guide',
            "## Reserving Meeting Spaces\n\n### Available Rooms\n\n- **Small Conference Rooms** (4-6 people): Rooms 101, 102, 201\n- **Medium Conference Rooms** (8-12 people): Rooms 150, 250\n- **Large Conference Room** (20+ people): Room 300 (Board Room)\n- **Training Room**: Room 175 (40 people, requires 1-week notice)\n\n### Booking Process\n\n1. Check availability in the room booking calendar\n2. Submit a booking request through the portal\n3. Include meeting purpose, attendee count, and equipment needs\n\n### Cancellation Policy\n\n- Cancel at least 4 hours in advance\n- No-shows for 3 consecutive bookings = booking privileges suspended for 30 days\n\n### Equipment Available\n\n- All rooms have video conferencing capability\n- Projectors in medium and large rooms\n- Whiteboards in all rooms\n- Catering requires 48-hour advance notice\n\nThe Board Room (300) requires executive assistant approval for non-executive bookings.",
            'CLUE-FAC-ROOMS-9965'
        );

        // Parking
        $this->createArticle($category, $status, $quality, $division,
            'Parking Information and Permits',
            "## Employee Parking\n\n### Parking Locations\n\n- **Main Lot A**: Employee parking, first-come first-served\n- **Lot B**: Overflow parking, farther from building\n- **Garage Level 1**: Reserved parking (available by request)\n- **Garage Level 2**: Visitor parking only\n\n### Parking Permits\n\n- Standard permits are free, issued by HR during onboarding\n- Reserved garage spots: \$75/month, deducted from payroll\n- Display permit on rearview mirror\n\n### Visitor Parking\n\n- Register visitors at reception\n- Visitors receive day pass for dashboard display\n- Maximum 4 hours in visitor spaces\n\n### Rules and Violations\n\n- Speed limit: 10 MPH\n- No parking in fire lanes, loading zones, or handicap spots without permit\n- Violations result in warning, then towing at owner's expense\n\nWinter parking rules: Snow emergency days, Lot B closes. Park in designated snow emergency areas.",
            'CLUE-FAC-PARK-4487'
        );

        // Building Access
        $this->createArticle($category, $status, $quality, $division,
            'Building Access and Security',
            "## Access Card Information\n\n### Standard Access\n\nYour access card provides:\n- Main entrance: 6 AM - 8 PM weekdays\n- Side entrance: 7 AM - 6 PM weekdays\n- Your assigned floor: 24/7\n\n### After-Hours Access\n\nAfter 8 PM and weekends:\n- Enter through main entrance only\n- Sign in at security desk\n- Escort required for visitors\n\n### Lost or Damaged Cards\n\n1. Report immediately to Security\n2. Old card will be deactivated\n3. Temporary card issued same day\n4. Permanent replacement within 3 business days\n5. \$25 replacement fee for lost cards\n\n### Special Access Requests\n\n- Server room access: IT manager approval required\n- Executive floor: Executive assistant approval\n- Lab areas: Safety training completion required\n\nThe building alarm code changes monthly. Current code is available from your department administrator.",
            'CLUE-FAC-ACCESS-6623'
        );

        // Cleaning Services
        $this->createArticle($category, $status, $quality, $division,
            'Cleaning Services and Requests',
            "## Building Cleaning Information\n\n### Regular Cleaning Schedule\n\n- **Common Areas**: Cleaned daily 6-8 AM\n- **Restrooms**: Cleaned 3x daily\n- **Break Rooms**: Cleaned daily, deep clean Fridays\n- **Individual Offices**: Trash/vacuum weekly\n\n### Requesting Extra Cleaning\n\nFor additional cleaning needs:\n1. Submit a service request through the portal\n2. Specify location and type of cleaning needed\n3. Note any urgency or timing requirements\n\n### Types of Cleaning Requests\n\n- **Spill cleanup**: Same-day response\n- **Restroom issues**: Priority response within 1 hour\n- **Deep cleaning**: Schedule 1 week in advance\n- **Event setup/cleanup**: 48-hour notice required\n\n### Green Cleaning Initiative\n\nWe use environmentally friendly cleaning products. If you have sensitivities, notify Facilities for accommodation options.\n\nThe cleaning supply closet code is available from your floor administrator. Do not use industrial cleaners without training.",
            'CLUE-FAC-CLEAN-5567'
        );

        // HVAC and Temperature
        $this->createArticle($category, $status, $quality, $division,
            'Temperature and HVAC Issues',
            "## Building Climate Control\n\n### Temperature Standards\n\n- Target temperature: 70-72°F (21-22°C)\n- Acceptable range: 68-74°F\n- Humidity maintained at 40-60%\n\n### Reporting Temperature Issues\n\n1. Check if others in the area are affected\n2. Note specific location (room number, near window, etc.)\n3. Submit service request with details\n4. Facilities will investigate within 4 hours\n\n### Common Causes\n\n- **Too hot**: Check for blocked vents, direct sunlight\n- **Too cold**: Verify windows are closed, check for drafts\n- **Inconsistent**: May indicate zone control issue\n\n### Seasonal Adjustments\n\n- Summer mode: Cooling priority\n- Winter mode: Heating priority\n- Transition periods: May have wider temperature swings\n\nPersonal space heaters are not permitted due to fire safety regulations. Contact Facilities if you need additional heating solutions. The HVAC system maintenance is performed quarterly, typically on the first Saturday of each quarter.",
            'CLUE-FAC-HVAC-8834'
        );

        // Keys and Physical Access
        $this->createArticle($category, $status, $quality, $division,
            'Requesting Keys and Physical Access',
            "## Key and Lock Services\n\n### Types of Keys\n\n- **Building Keys**: For exterior doors\n- **Office Keys**: For individual offices\n- **File Cabinet Keys**: For secure storage\n- **Specialty Keys**: Labs, storage rooms\n\n### Requesting Keys\n\n1. Submit service request with manager approval\n2. Specify key type and access needed\n3. Pick up from Facilities (Room 50) with ID\n4. Sign key custody agreement\n\n### Lost Keys\n\n- Report immediately to Facilities and Security\n- Lock may need to be re-keyed (departmental charge)\n- Temporary key issued while rekeying occurs\n- Replacement timeline: 3-5 business days\n\n### Returning Keys\n\n- Required upon termination or transfer\n- Return to Facilities or HR during exit process\n- Unreturned keys: \$50 charge per key\n\nMaster keys are never issued to individual employees. For after-hours access, contact Security at ext. 9999.",
            'CLUE-FAC-KEYS-7721'
        );

        // Office Moves and Furniture
        $this->createArticle($category, $status, $quality, $division,
            'Office Moves and Furniture Requests',
            "## Moving and Furniture Services\n\n### Office Relocations\n\nFor office moves:\n1. Submit request at least 2 weeks in advance\n2. Get manager and destination manager approval\n3. Facilities will coordinate IT for equipment\n4. Pack personal items; movers handle furniture\n\n### Furniture Requests\n\n- **Standard Items**: Desk, chair, filing cabinet available from inventory\n- **Ergonomic Equipment**: Requires HR accommodation approval\n- **New Purchases**: Departmental budget approval needed\n\n### Timeline\n\n- From inventory: 3-5 business days\n- New orders: 2-4 weeks\n- Ergonomic items: 1-2 weeks after approval\n\n### Disposal and Recycling\n\n- Old furniture picked up during moves\n- Do not place furniture in hallways\n- Electronics disposed through IT, not Facilities\n\nLarge moves (5+ people) require 1-month notice and are scheduled for weekends to minimize disruption.",
            'CLUE-FAC-MOVE-4492'
        );
    }

    protected function createStudentServicesArticles(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division
    ): void {
        // Enrollment
        $this->createArticle($category, $status, $quality, $division,
            'Course Registration and Enrollment',
            "## Registration Process\n\n### Registration Windows\n\n- Seniors: Opens November 1\n- Juniors: Opens November 5\n- Sophomores: Opens November 10\n- Freshmen: Opens November 15\n\n### How to Register\n\n1. Log in to the student portal\n2. Navigate to Registration tab\n3. Search for courses by department, number, or keyword\n4. Add courses to your cart\n5. Submit registration\n\n### Prerequisites\n\nThe system automatically checks prerequisites. If you need an override:\n- Contact the department offering the course\n- Get written approval from instructor\n- Submit override request with approval attached\n\n### Waitlists\n\n- Join waitlist if course is full\n- You'll be notified if a spot opens\n- Must confirm within 24 hours or lose spot\n\nThe registration system undergoes maintenance every Sunday 2-4 AM. Plan your registration accordingly.",
            'CLUE-STU-ENROLL-8834'
        );

        // Financial Aid
        $this->createArticle($category, $status, $quality, $division,
            'Financial Aid Application Process',
            "## Applying for Financial Aid\n\n### FAFSA Requirements\n\n- Complete FAFSA at studentaid.gov\n- School code: 002345\n- Priority deadline: March 1\n- Regular deadline: June 30\n\n### Required Documents\n\n- Student and parent tax returns\n- W-2 forms\n- Bank statements\n- Records of untaxed income\n\n### Award Notification\n\n- Aid packages sent by April 15 (priority applicants)\n- Accept/decline/modify in student portal\n- Deadline to accept: May 1\n\n### Types of Aid Available\n\n- Grants (need-based, no repayment)\n- Scholarships (merit-based, no repayment)\n- Federal loans (subsidized and unsubsidized)\n- Work-study programs\n\nThe Financial Aid Office is located in Student Services Building, Room 105. Drop-in hours are Monday-Thursday 10 AM - 3 PM.",
            'CLUE-STU-FINAID-2291'
        );

        // Transcripts
        $this->createArticle($category, $status, $quality, $division,
            'Ordering Academic Transcripts',
            "## Transcript Requests\n\n### Online Ordering\n\n1. Log in to student portal\n2. Navigate to Records > Transcripts\n3. Select official or unofficial\n4. Choose delivery method\n5. Pay fee (if applicable)\n\n### Transcript Types\n\n- **Official Electronic**: \$5, delivered within 24 hours\n- **Official Paper**: \$10, mailed within 3-5 business days\n- **Unofficial**: Free, immediate download\n\n### Holds on Records\n\nTranscripts cannot be released if you have:\n- Unpaid tuition balance\n- Library fines over \$25\n- Outstanding parking tickets\n- Incomplete immunization records\n\n### Rush Processing\n\n- Same-day service: Additional \$15\n- Available Monday-Friday before 2 PM\n- Not available during finals week\n\nFor transcripts from schools attended before 2010, contact the Registrar's Office directly as these may require additional processing time.",
            'CLUE-STU-TRANS-5567'
        );

        // Disability Services
        $this->createArticle($category, $status, $quality, $division,
            'Disability Accommodations Guide',
            "## Requesting Academic Accommodations\n\n### Getting Started\n\n1. Contact the Disability Services Office\n2. Submit documentation of disability\n3. Meet with a disability specialist\n4. Receive accommodation letter\n5. Share letter with instructors\n\n### Documentation Requirements\n\n- Medical or psychological evaluation\n- Must be from licensed professional\n- Should be within last 3 years\n- Include specific accommodation recommendations\n\n### Common Accommodations\n\n- Extended test time (typically 1.5x)\n- Separate testing location\n- Note-taking assistance\n- Priority registration\n- Accessible seating\n- Recording permission\n\n### Testing Center\n\nThe Testing Center (Library Building, Room 50) provides:\n- Quiet testing environment\n- Extended time administration\n- Computer access with assistive technology\n\nAccommodation letters must be renewed each semester. Request renewal by the first week of classes.",
            'CLUE-STU-DISAB-7732'
        );

        // Advising
        $this->createArticle($category, $status, $quality, $division,
            'Academic Advising Information',
            "## Meeting with Your Advisor\n\n### Finding Your Advisor\n\nYour assigned advisor is listed in the student portal under \"Academic Info.\"\n\n### Scheduling Appointments\n\n- Online: Through student portal calendar\n- Phone: Department office during business hours\n- Walk-ins: Available but may have wait times\n\n### What to Bring\n\n- Unofficial transcript\n- Degree audit printout\n- List of questions\n- Course schedule ideas\n\n### Advising Topics\n\n- Course selection and sequencing\n- Major/minor declarations\n- Graduation requirements\n- Academic difficulties\n- Career planning\n\n### Degree Audit\n\nThe degree audit tool shows:\n- Completed requirements (green)\n- In-progress courses (yellow)\n- Remaining requirements (red)\n\nAll students must meet with an advisor before registering for the first time. The advising hold is removed after the meeting.",
            'CLUE-STU-ADVISE-9918'
        );

        // Waitlist and Course Availability
        $this->createArticle($category, $status, $quality, $division,
            'Course Waitlists and Full Classes',
            "## Managing Course Waitlists\n\n### How Waitlists Work\n\n1. When a course is full, join the waitlist\n2. System notifies you when spot opens\n3. You have 24 hours to accept\n4. If you don't act, next person gets the spot\n\n### Waitlist Position\n\nCheck your position in the student portal under Registration > Waitlisted Courses.\n\n### Tips for Getting into Full Courses\n\n- Join waitlist immediately when course fills\n- Check for newly opened sections\n- Attend first class even if waitlisted (instructor may add you)\n- Email instructor explaining your situation\n\n### Course Section Changes\n\n- Additional sections may be added based on demand\n- Check portal daily during add/drop period\n- Evening and online sections often have availability\n\n### Override Requests\n\nFor required courses with no available sections:\n1. Email department chair\n2. Explain graduation impact\n3. Request capacity override\n\nThe add/drop period ends two weeks after classes begin. After this, instructor and dean approval is required.",
            'CLUE-STU-WAITLIST-6634'
        );

        // Academic Standing
        $this->createArticle($category, $status, $quality, $division,
            'Understanding Academic Standing',
            "## Academic Standing Policies\n\n### GPA Requirements\n\n- Good Standing: 2.0 or higher cumulative GPA\n- Academic Warning: Below 2.0 for one semester\n- Academic Probation: Below 2.0 for two consecutive semesters\n- Academic Suspension: Below 2.0 for three consecutive semesters\n\n### Returning from Probation\n\n1. Meet with academic advisor\n2. Create an academic improvement plan\n3. May have course load restrictions\n4. Required to use tutoring services\n\n### Appeals Process\n\nIf you believe your standing is incorrect:\n1. Submit written appeal to Academic Standards Committee\n2. Include documentation and explanation\n3. Deadline: 10 business days after notification\n\n### Resources for Struggling Students\n\n- Tutoring Center: Free tutoring in most subjects\n- Writing Center: Help with papers and essays\n- Study Skills Workshops: Time management, note-taking\n- Counseling Services: Stress management, personal issues\n\nStudents on probation are required to attend a success workshop within the first two weeks of the semester.",
            'CLUE-STU-STANDING-7781'
        );

        // Student Financial Accounts
        $this->createArticle($category, $status, $quality, $division,
            'Student Account and Billing',
            "## Understanding Your Student Account\n\n### Viewing Your Bill\n\n1. Log in to student portal\n2. Go to Financial > Account Summary\n3. View current balance and payment history\n\n### Charges and Fees\n\n- Tuition: Based on credit hours and residency\n- Student fees: Technology, activity, health center\n- Housing: If living on campus\n- Meal plan: If selected\n\n### Payment Options\n\n- Online payment (credit card, bank transfer)\n- Payment plan (4 installments per semester)\n- Financial aid applied automatically\n- Third-party billing (employer, sponsor)\n\n### Important Deadlines\n\n- Payment due: 1st day of classes\n- Late fee: \$150 after due date\n- Payment plan enrollment: 2 weeks before due date\n\n### Holds on Account\n\nUnpaid balances result in:\n- Registration hold\n- Transcript hold\n- Diploma hold\n\nFor balance over \$5,000, contact the Bursar's Office for hardship options. The payment portal is available 24/7 at pay.university.edu.",
            'CLUE-STU-BILLING-3349'
        );

        // Graduation Requirements
        $this->createArticle($category, $status, $quality, $division,
            'Graduation Application and Requirements',
            "## Preparing for Graduation\n\n### Application Deadlines\n\n- Spring graduation: Apply by October 1\n- Summer graduation: Apply by February 1\n- Fall graduation: Apply by June 1\n\n### Requirements Checklist\n\n- Minimum 120 credit hours (varies by program)\n- Cumulative GPA of 2.0 or higher\n- Major GPA of 2.5 or higher\n- All general education requirements complete\n- All major requirements complete\n- No outstanding holds\n\n### Applying to Graduate\n\n1. Log in to student portal\n2. Navigate to Graduation > Apply\n3. Verify your information\n4. Select ceremony attendance\n5. Order cap and gown if attending\n\n### After Applying\n\n- Registrar reviews within 4 weeks\n- Deficiencies emailed to you\n- Meet with advisor to resolve issues\n- Final clearance 2 weeks before graduation\n\nDiplomas are mailed 6-8 weeks after graduation. Update your address in the portal if needed. The graduation application fee is \$75, non-refundable.",
            'CLUE-STU-GRAD-5512'
        );
    }

    protected function createFinancialArticles(
        KnowledgeBaseCategory $category,
        KnowledgeBaseStatus $status,
        KnowledgeBaseQuality $quality,
        ?Division $division
    ): void {
        // Expense Reimbursement
        $this->createArticle($category, $status, $quality, $division,
            'Expense Reimbursement Guidelines',
            "## Submitting Expenses for Reimbursement\n\n### Eligible Expenses\n\n- Business travel (airfare, hotel, ground transport)\n- Meals during travel (per diem rates apply)\n- Conference registration fees\n- Business supplies (pre-approved)\n- Mileage for business use of personal vehicle\n\n### Submission Process\n\n1. Collect all receipts\n2. Complete expense report in financial portal\n3. Attach receipt images\n4. Submit for manager approval\n5. Reimbursement processed within 10 business days\n\n### Receipt Requirements\n\n- Must be itemized (not credit card summary)\n- Include vendor name, date, amount\n- Alcohol never reimbursable\n- Tips limited to 20%\n\n### Mileage Rates\n\n- Current rate: \$0.67 per mile\n- Must document starting point, destination, purpose\n- Commute mileage is not reimbursable\n\nThe expense report deadline is the 5th of the following month. Late submissions may be delayed to the next payment cycle.",
            'CLUE-FIN-EXPENSE-3384'
        );

        // Travel Policy
        $this->createArticle($category, $status, $quality, $division,
            'Business Travel Policy',
            "## Travel Booking and Policies\n\n### Booking Requirements\n\n- Book through approved travel portal (Concur)\n- Air travel: Economy class for flights under 6 hours\n- Hotels: Use preferred vendors, max \$200/night in most cities\n- Rental cars: Compact or midsize only\n\n### Pre-Trip Approval\n\n- Submit travel request at least 2 weeks in advance\n- Include estimated costs and business purpose\n- International travel requires VP approval\n\n### Per Diem Rates\n\n- Breakfast: \$15\n- Lunch: \$20\n- Dinner: \$35\n- Rates vary for high-cost cities (NYC, SF, DC)\n\n### Travel Advances\n\n- Available for trips over 3 days\n- Request at least 1 week before travel\n- Reconcile within 5 business days of return\n\nTravel insurance is provided for all business travel. Details and coverage information are in the travel portal under \"Resources.\"",
            'CLUE-FIN-TRAVEL-6617'
        );

        // P-Card
        $this->createArticle($category, $status, $quality, $division,
            'Purchasing Card (P-Card) Guide',
            "## Using Your P-Card\n\n### Card Limits\n\n- Single transaction: \$2,500\n- Monthly limit: \$10,000\n- Exceptions require director approval\n\n### Approved Purchases\n\n- Office supplies\n- Software subscriptions (under \$500/year)\n- Conference registrations\n- Small equipment (under \$1,000)\n\n### Prohibited Purchases\n\n- Personal items\n- Gift cards\n- Cash advances\n- Alcohol\n- Travel (use travel portal instead)\n\n### Monthly Reconciliation\n\n1. Log in to P-Card portal\n2. Review all transactions\n3. Attach receipts to each transaction\n4. Assign correct GL codes\n5. Submit by the 15th of the following month\n\n### Lost or Stolen Cards\n\nReport immediately to:\n- P-Card Administrator: ext. 5500\n- Card issuer: 1-800-555-CARD (after hours)\n\nUnreconciled transactions will result in temporary card suspension.",
            'CLUE-FIN-PCARD-8892'
        );

        // Budget
        $this->createArticle($category, $status, $quality, $division,
            'Budget Management and Reporting',
            "## Understanding Your Department Budget\n\n### Budget Structure\n\n- Operating expenses (day-to-day costs)\n- Capital expenses (equipment over \$5,000)\n- Personnel (salaries and benefits)\n\n### Accessing Budget Reports\n\n1. Log in to financial portal\n2. Navigate to Reports > Budget\n3. Select your cost center\n4. Choose date range\n\n### Budget Transfers\n\n- Within same category: Manager approval\n- Between categories: Director approval\n- From personnel to operating: VP approval\n\n### Year-End Procedures\n\n- Fiscal year ends June 30\n- Submit all invoices by June 15\n- Unused operating funds do not roll over\n- Capital budget carries over if committed\n\n### Variance Reporting\n\nExplain variances over 10%:\n- Positive variance: Underspending reasons\n- Negative variance: Overspending justification\n\nBudget meetings with Finance are held quarterly. Check with your department admin for scheduled times.",
            'CLUE-FIN-BUDGET-1156'
        );

        // Invoice Processing
        $this->createArticle($category, $status, $quality, $division,
            'Submitting Vendor Invoices for Payment',
            "## Invoice Submission Process\n\n### Requirements for Payment\n\n- Original invoice (PDF acceptable)\n- Purchase order number (if over \$1,000)\n- Three-way match: PO, receipt, invoice\n- Proper GL coding\n\n### Submission Methods\n\n- Portal upload (preferred)\n- Email to invoices@company.com\n- Physical delivery to AP, Room 125\n\n### Payment Terms\n\n- Standard: Net 30 days\n- Early payment discount: Net 10/2%\n- Rush payment: Requires VP approval\n\n### New Vendor Setup\n\nFor first-time vendors:\n1. Complete vendor registration form\n2. Obtain W-9 from vendor\n3. Submit for compliance review\n4. Allow 5 business days for setup\n\n### Payment Status\n\nCheck payment status in the vendor portal. For urgent inquiries, contact AP at ext. 5501.\n\nPayment runs occur every Tuesday and Thursday. Invoices received after noon Thursday will be processed the following week.",
            'CLUE-FIN-INVOICE-4429'
        );

        // P-Card Lost/Stolen
        $this->createArticle($category, $status, $quality, $division,
            'Lost or Stolen P-Card Procedures',
            "## Reporting Lost or Stolen P-Cards\n\n### Immediate Steps\n\n1. Call the card issuer immediately: 1-800-555-CARD\n2. Report to P-Card Administrator: ext. 5500\n3. Complete the Lost Card Report form\n4. Review recent transactions for fraud\n\n### Replacement Process\n\n- Temporary card: Available within 24 hours\n- Permanent replacement: 5-7 business days\n- New card will have new number\n\n### Fraudulent Charges\n\nIf you notice unauthorized transactions:\n1. Report to card issuer immediately\n2. Document dates, amounts, vendors\n3. Complete fraud dispute form\n4. Cooperate with investigation\n\n### Prevention Tips\n\n- Keep card in secure location\n- Don't share card number via email\n- Review transactions weekly\n- Report suspicious activity immediately\n\nYou are not liable for fraudulent charges if reported within 48 hours. After 48 hours, liability may apply up to \$50.",
            'CLUE-FIN-LOSTCARD-8876'
        );

        // Purchase Orders
        $this->createArticle($category, $status, $quality, $division,
            'Creating and Managing Purchase Orders',
            "## Purchase Order Process\n\n### When POs are Required\n\n- Orders over \$1,000\n- Services and contracts\n- Multi-year agreements\n- Capital equipment purchases\n\n### Creating a Purchase Order\n\n1. Log in to financial portal\n2. Go to Purchasing > New PO\n3. Enter vendor and item details\n4. Route for approvals\n5. Send to vendor after approval\n\n### Approval Thresholds\n\n- Under \$5,000: Manager approval\n- \$5,000-\$25,000: Director approval\n- Over \$25,000: VP approval + Procurement review\n\n### PO Status Tracking\n\n- Draft: Not yet submitted\n- Pending: Awaiting approval\n- Approved: Ready to send\n- Open: Sent to vendor\n- Closed: Fully received/paid\n\n### Receiving Against POs\n\nWhen goods arrive:\n1. Verify quantity matches PO\n2. Inspect for damage\n3. Enter receipt in system\n4. Forward invoice to AP\n\nPOs expire after 1 year if not used. Contact Purchasing for extensions.",
            'CLUE-FIN-PO-7734'
        );

        // GL Codes and Cost Centers
        $this->createArticle($category, $status, $quality, $division,
            'Understanding GL Codes and Cost Centers',
            "## Financial Coding Guide\n\n### What is a GL Code?\n\nGeneral Ledger (GL) codes classify expenses by type:\n- 6100: Salaries and wages\n- 6200: Benefits\n- 6300: Travel and entertainment\n- 6400: Supplies and materials\n- 6500: Professional services\n- 6600: Equipment and software\n- 6700: Facilities and utilities\n\n### What is a Cost Center?\n\nCost centers identify which department is charged:\n- Format: CC-DEPT-YEAR (e.g., CC-IT-2026)\n- Each department has unique cost centers\n- Some projects have dedicated cost centers\n\n### Finding Your Codes\n\n- Your cost center: Listed in the HR portal under \"Job Details\"\n- GL code list: Available in financial portal under \"References\"\n- Project codes: Contact your project manager\n\n### Common Coding Errors\n\n- Wrong fiscal year in cost center\n- Using closed project codes\n- Misclassifying expense types\n\nWhen in doubt, contact Finance at ext. 5510. Miscoded expenses delay processing and require corrections.",
            'CLUE-FIN-GLCODE-5521'
        );

        // Fiscal Year End
        $this->createArticle($category, $status, $quality, $division,
            'Fiscal Year End Procedures',
            "## Year-End Financial Closing\n\n### Important Dates\n\n- Fiscal year ends: June 30\n- Last day for POs: June 15\n- Last day for invoices: June 20\n- Last day for expense reports: June 25\n\n### What Carries Over\n\n- Encumbered purchase orders\n- Capital project budgets\n- Grant funds (per grant terms)\n\n### What Does NOT Carry Over\n\n- Operating budget balances\n- Unspent supply funds\n- Travel allocations\n\n### Year-End Checklist\n\n1. Review open POs - close or extend\n2. Submit pending invoices\n3. Complete expense reports\n4. Verify budget coding accuracy\n5. Address any pending approvals\n\n### Accruals\n\nFor goods/services received but not yet invoiced:\n- Submit accrual form by June 25\n- Include estimated amount and vendor\n- Provides budget coverage into new year\n\nYear-end processing takes approximately 3 weeks. New fiscal year reports available by July 21.",
            'CLUE-FIN-YEAREND-9943'
        );

        // Contract and Vendor Management
        $this->createArticle($category, $status, $quality, $division,
            'Working with Vendors and Contracts',
            "## Vendor and Contract Guidelines\n\n### Selecting Vendors\n\n- Check preferred vendor list first\n- Competitive bids required over \$10,000\n- Sole source requires justification\n- Procurement can assist with sourcing\n\n### Contract Requirements\n\nAll contracts must include:\n- Statement of work\n- Payment terms\n- Insurance requirements\n- Termination clauses\n\n### Contract Approval\n\n- Under \$25,000: Department head\n- \$25,000-\$100,000: Legal review + VP\n- Over \$100,000: Legal + Finance + Executive\n\n### Vendor Performance Issues\n\nIf a vendor fails to perform:\n1. Document the issues\n2. Notify Procurement\n3. Send formal notice to vendor\n4. Consider contract remedies\n\n### Payment Best Practices\n\n- Verify work completion before payment\n- Don't pay in advance without approval\n- Report invoice discrepancies immediately\n\nPreferred vendors offer pre-negotiated rates and terms. Search the vendor database in the financial portal before engaging new vendors.",
            'CLUE-FIN-VENDOR-2238'
        );
    }

    protected function enableAISupportAndIndex(): void
    {
        $this->command->info('Enabling AI support and triggering knowledge base indexing...');

        $settings = app(PortalSettings::class);
        $settings->ai_support_assistant = true;
        $settings->knowledge_management_portal_enabled = true;
        $settings->save();

        // Dispatch the job to index knowledge base items
        PrepareKnowledgeBaseVectorStore::dispatch();

        $this->command->info('AI indexing job dispatched.');
    }
}
