<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $status = DB::table('service_request_statuses')->where([
            'name' => 'New',
            'classification' => 'open',
        ])->first();

        if ($status && $status->is_system_protected) {
            return;
        }

        if ($status) {
            DB::table('service_request_statuses')
                ->where('name', 'New')
                ->where('classification', 'open')
                ->update([
                    'is_system_protected' => true,
                ]);

            return;
        }

        DB::table('service_request_statuses')->insert([
            'name' => 'New',
            'classification' => 'open',
            'color' => 'info',
            'is_system_protected' => true,
        ]);
    }

    public function down(): void
    {
        DB::table('service_request_statuses')
            ->where('name', 'New')
            ->where('classification', 'open')
            ->update([
                'is_system_protected' => false,
            ]);
    }
};
