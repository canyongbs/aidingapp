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

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    public function up(): void
    {
        $this->deletePermissions([
            'timeline.access',
            'authorization.impersonate',
            'authorization.view_dashboard',
            'authorization.view_api_documentation',
            'amazon-ses.manage_ses_settings',
        ], guardName: 'web');

        $this->renamePermissions([
            'in-app-communication.realtime-chat.access' => 'realtime_chat.view-any',
        ], guardName: 'web');

        $this->renamePermissionGroups([
            'In-App Communication' => 'Realtime Chat',
        ]);

        $this->createPermissions([
            'realtime_chat.*.view' => 'Realtime Chat',
        ], guardName: 'web');

        DB::table('permissions')
            ->whereRaw("name ~ '^task\\.[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}\\.update$'")
            ->delete();
    }

    public function down(): void
    {
        $this->deletePermissions([
            'realtime_chat.*.view',
        ], guardName: 'web');

        $this->renamePermissionGroups([
            'Realtime Chat' => 'In-App Communication',
        ]);

        $this->renamePermissions([
            'realtime_chat.view-any' => 'in-app-communication.realtime-chat.access',
        ], guardName: 'web');

        $this->createPermissions([
            'timeline.access' => 'Timeline',
            'authorization.impersonate' => 'Authorization',
            'authorization.view_dashboard' => 'Authorization',
            'authorization.view_api_documentation' => 'Authorization',
            'amazon-ses.manage_ses_settings' => 'Amazon SES',
        ], guardName: 'web');
    }
};
