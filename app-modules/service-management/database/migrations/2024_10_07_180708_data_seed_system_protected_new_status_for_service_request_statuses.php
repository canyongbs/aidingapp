<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('service_request_statuses')->upsert(
            [
                'name' => 'New',
                'classification' => 'open',
                'color' => 'info',
                'is_system_protected' => true,
            ],
            [
                'name',
                'classification',
            ],
            ['is_system_protected']
        );
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
