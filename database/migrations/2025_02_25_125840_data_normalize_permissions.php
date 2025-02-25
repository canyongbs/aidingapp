<?php

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
