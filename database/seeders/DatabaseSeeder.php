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

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use AidingApp\Task\Database\Seeders\TaskSeeder;
use AidingApp\Team\Database\Seeders\TeamSeeder;
use AidingApp\Alert\Database\Seeders\AlertSeeder;
use AidingApp\Contact\Database\Seeders\ContactSeeder;
use AidingApp\Assistant\Database\Seeders\PromptSeeder;
use AidingApp\Division\Database\Seeders\DivisionSeeder;
use AidingApp\Assistant\Database\Seeders\PromptTypeSeeder;
use AidingApp\Contact\Database\Seeders\ContactSourceSeeder;
use AidingApp\Contact\Database\Seeders\ContactStatusSeeder;
use AidingApp\Consent\Database\Seeders\ConsentAgreementSeeder;
use AidingApp\InventoryManagement\Database\Seeders\AssetSeeder;
use AidingApp\KnowledgeBase\Database\Seeders\KnowledgeBaseItemSeeder;
use AidingApp\ServiceManagement\Database\Seeders\ServiceRequestSeeder;
use AidingApp\KnowledgeBase\Database\Seeders\KnowledgeBaseStatusSeeder;
use AidingApp\KnowledgeBase\Database\Seeders\KnowledgeBaseQualitySeeder;
use AidingApp\KnowledgeBase\Database\Seeders\KnowledgeBaseCategorySeeder;
use AidingApp\ServiceManagement\Database\Seeders\ChangeRequestTypeSeeder;
use AidingApp\ServiceManagement\Database\Seeders\ServiceRequestTypeSeeder;
use AidingApp\ServiceManagement\Database\Seeders\ChangeRequestStatusSeeder;
use AidingApp\ServiceManagement\Database\Seeders\ServiceRequestStatusSeeder;
use AidingApp\ServiceManagement\Database\Seeders\ServiceRequestUpdateSeeder;
use AidingApp\InventoryManagement\Database\Seeders\MaintenanceProviderSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reduce notifications sent during seeding
        Notification::fake();

        $currentTenant = Tenant::current();

        $this->call([
            SampleSuperAdminUserSeeder::class,
            DivisionSeeder::class,
            ServiceRequestStatusSeeder::class,
            ServiceRequestTypeSeeder::class,
            ContactStatusSeeder::class,
            ContactSourceSeeder::class,
            KnowledgeBaseCategorySeeder::class,
            KnowledgeBaseQualitySeeder::class,
            KnowledgeBaseStatusSeeder::class,
            ConsentAgreementSeeder::class,
            PronounsSeeder::class,

            ServiceRequestSeeder::class,
            ServiceRequestUpdateSeeder::class,
            ContactSeeder::class,
            KnowledgeBaseItemSeeder::class,
            TaskSeeder::class,
            AlertSeeder::class,
            TeamSeeder::class,
            SuperAdminSeeder::class,
            TwilioContactSeeder::class,

            // InventoryManagement
            ...AssetSeeder::metadataSeeders(),
            AssetSeeder::class,
            MaintenanceProviderSeeder::class,

            // Change Request
            ChangeRequestTypeSeeder::class,
            ChangeRequestStatusSeeder::class,

            PromptTypeSeeder::class,
            PromptSeeder::class,

            ContactSeeder::class,
        ]);
    }
}
