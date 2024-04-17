<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseCategory::factory()
            ->createMany(
                [
                    ['name' => 'Password Management', 'description' => 'Information and procedures related to password policies, password reset processes, and best practices for creating strong passwords.', 'icon' => null],
                    ['name' => 'Software Installation/Configuration', 'description' => 'Guides and documentation for installing, configuring, and updating various software applications used within the organization.', 'icon' => null],
                    ['name' => 'Hardware Setup/Configuration', 'description' => 'Resources and instructions for setting up and configuring hardware devices such as computers, printers, scanners, and networking equipment.', 'icon' => null],
                    ['name' => 'Network Troubleshooting', 'description' => 'Articles and troubleshooting guides for diagnosing and resolving common network connectivity issues, including Wi-Fi, Ethernet, and VPN problems.', 'icon' => null],
                    ['name' => 'Email Setup/Configuration', 'description' => 'Documentation for setting up email accounts, configuring email clients, and troubleshooting common email issues such as sending/receiving problems or spam filtering.', 'icon' => null],
                    ['name' => 'Access Control/Permissions', 'description' => 'Information on managing access permissions to files, folders, databases, and applications, including user roles and privileges.', 'icon' => null],
                    ['name' => 'Data Backup and Recovery', 'description' => 'Guides for implementing data backup procedures, selecting backup solutions, and recovering data from backups in case of data loss or corruption.', 'icon' => null],
                    ['name' => 'Cybersecurity Best Practices', 'description' => 'Articles on cybersecurity best practices, including tips for protecting against viruses, malware, phishing attacks, and other security threats.', 'icon' => null],
                    ['name' => 'Hardware Maintenance/Repair', 'description' => 'Documentation for performing routine hardware maintenance tasks, troubleshooting hardware problems, and replacing faulty components.', 'icon' => null],
                    ['name' => 'Training and User Guides', 'description' => 'Resources for providing IT training to users, including user manuals, video tutorials, and FAQs for common IT tasks and software applications.', 'icon' => null],
                ]
            );
    }
}
