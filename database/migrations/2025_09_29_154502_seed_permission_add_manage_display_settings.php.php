<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('permission_groups')
            ->insert([
                'id' => $groupId = Str::orderedUuid(),
                'name' => 'Display Settings',
                'created_at' => now(),
            ]);

        DB::table('permissions')
            ->insert([
                'id' => Str::orderedUuid(),
                'name' => 'display_settings.manage',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'display_settings.manage')
            ->delete();

        DB::table('permission_groups')
            ->where('name', 'Display Settings')
            ->delete();
    }
};
