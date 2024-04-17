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

namespace AidingApp\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class ServiceRequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestType::factory()
            ->createMany(
                [
                    ['name' => 'Admissions', 'description' => null, 'icon' => null],
                    ['name' => 'Advising', 'description' => null, 'icon' => null],
                    ['name' => 'ESL', 'description' => null, 'icon' => null],
                    ['name' => 'Financial', 'description' => null, 'icon' => null],
                    ['name' => 'Health', 'description' => null, 'icon' => null],
                    ['name' => 'Holds', 'description' => null, 'icon' => null],
                    ['name' => 'International', 'description' => null, 'icon' => null],
                    ['name' => 'Other Support', 'description' => null, 'icon' => null],
                    ['name' => 'Recruitment', 'description' => null, 'icon' => null],
                    ['name' => 'Technology', 'description' => null, 'icon' => null],
                    ['name' => 'Tutoring', 'description' => null, 'icon' => null],
                    ['name' => 'Veterans', 'description' => null, 'icon' => null],
                    ['name' => 'Password Reset', 'description' => 'Users often forget their passwords or need them reset for security reasons.', 'icon' => null],
                    ['name' => 'Software Installation/Upgrades', 'description' => 'Requests for installing new software or updating existing ones.', 'icon' => null ],
                    ['name' => 'Hardware Setup/Configuration', 'description' => 'Assistance with setting up new hardware devices such as computers, printers, or network equipment.', 'icon' => null],
                    ['name' => 'Network Connectivity Issues','description' => 'Help with troubleshooting and resolving issues related to network connectivity, such as Wi-Fi problems or Ethernet connection issues.','icon' => null],
                    ['name' => 'Email Configuration/Issues','description' => 'Assistance with setting up email accounts, configuring email clients, or troubleshooting email-related problems.','icon' => null],
                    ['name' => 'Access Permissions/Security Requests','description' => 'Requests for granting or revoking access permissions to files, folders, or applications, as well as security-related inquiries.','icon' => null],
                    ['name' => 'Data Backup/Recovery','description' => 'Requests for assistance with data backup procedures or recovering lost or corrupted data.','icon' => null],
                    ['name' => 'Virus/Malware Removal','description' => 'Assistance with identifying and removing viruses, malware, or other security threats from computers or networks.','icon' => null],
                    ['name' => 'Hardware Repair/Replacement','description' => 'Requests for repairing or replacing faulty hardware components such as hard drives, RAM, or motherboards.','icon' => null],
                    ['name' => 'Training/Instruction','description' => 'Requests for IT training or guidance on using specific software applications or IT systems effectively.','icon' => null]
                ]
            )
            ->each(function (ServiceRequestType $type) {
                $type->priorities()->createMany(
                    [
                        ['name' => 'High', 'order' => 1],
                        ['name' => 'Medium', 'order' => 2],
                        ['name' => 'Low', 'order' => 3],
                    ]
                );
            });
    }
}
