<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $groupId = (string) Str::orderedUuid();

        DB::table('permission_groups')
            ->insert(
                [
                    'id' => $groupId,
                    'name' => 'Amazon SES',
                    'created_at' => now(),
                ]
            );

        DB::table('permissions')->insert(
            [
                'id' => (string) Str::orderedUuid(),
                'group_id' => $groupId,
                'guard_name' => 'web',
                'name' => 'amazon-ses.manage_ses_settings',
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'amazon-ses.manage_ses_settings')
            ->delete();

        DB::table('permission_groups')
            ->where('name', 'Amazon SES')
            ->delete();
    }
};
