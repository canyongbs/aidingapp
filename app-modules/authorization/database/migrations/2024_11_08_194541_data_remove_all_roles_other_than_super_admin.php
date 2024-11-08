<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')
            ->where('name', '!=', 'authorization.super_admin')
            ->orderBy('id')
            ->chunk(100, function (Collection $roles) {
                foreach ($roles as $role) {
                    DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                    DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
                    DB::table('roles')->where('id', $role->id)->delete();
                }
            });
    }

    public function down(): void
    {
        // Cannot be reversed
    }
};
