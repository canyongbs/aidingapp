<?php

use App\Models\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')->where('name', 'authorization.super_admin')->update(['name' => Authenticatable::SUPER_ADMIN_ROLE]);
    }

    public function down(): void
    {
        DB::table('roles')->where('name', Authenticatable::SUPER_ADMIN_ROLE)->update(['name' => 'authorization.super_admin']);
    }
};
