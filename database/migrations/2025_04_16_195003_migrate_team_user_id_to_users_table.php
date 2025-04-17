<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('team_user')->get()->each(function ($teamUser) {
            DB::table('users')
                ->where('id', $teamUser->user_id)
                ->update(['team_id' => $teamUser->team_id]);
        });
    }

    public function down(): void
    {
        DB::table('users')->get()->each(function ($teamUser) {
            DB::table('team_user')->create([
                'team_id' => $teamUser->team_id,
                'user_id' => $teamUser->user_id,
            ]);
        });
    }
};
