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

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        // TODO: When we eventually have a paradigm for validating and retrieving license data, change this to both default to null, nothing passed into the second argument

        $this->migrator->addEncrypted('license.license_key', 'ABCD-1234-EFGH-5678');
        $this->migrator->addEncrypted(
            'license.data',
            [
                'updated_at' => now(),
                'subscription' => [
                    'client_name' => 'Jane Smith',
                    'partner_name' => 'Fake Edu Tech',
                    'client_po' => 'abc123',
                    'partner_po' => 'def456',
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                ],
                'limits' => [
                    'conversational_ai_seats' => 50,
                    'retention_crm_seats' => 25,
                    'recruitment_crm_seats' => 10,
                    'emails' => 1000,
                    'sms' => 1000,
                    'reset_date' => now()->format('m-d'),
                ],
                'addons' => [
                    'online_forms' => true,
                    'online_surveys' => true,
                    'online_admissions' => true,
                    'service_management' => true,
                    'knowledge_management' => true,
                    'event_management' => true,
                    'realtime_chat' => true,
                    'mobileApps' => true,
                ],
            ]
        );
    }
};
