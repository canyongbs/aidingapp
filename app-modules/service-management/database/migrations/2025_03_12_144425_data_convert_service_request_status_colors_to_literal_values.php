<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('alter table service_request_statuses disable trigger prevent_modification_of_system_protected_rows');

            DB::table('service_request_statuses')
                ->where('color', 'danger')
                ->update(['color' => 'red']);

            DB::table('service_request_statuses')
                ->whereIn('color', ['warning', 'primary'])
                ->update(['color' => 'amber']);

            DB::table('service_request_statuses')
                ->where('color', 'success')
                ->update(['color' => 'green']);

            DB::table('service_request_statuses')
                ->where('color', 'info')
                ->update(['color' => 'blue']);

            DB::statement('alter table service_request_statuses enable trigger prevent_modification_of_system_protected_rows');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('alter table service_request_statuses disable trigger prevent_modification_of_system_protected_rows');

            DB::table('service_request_statuses')
                ->where('color', 'red')
                ->update(['color' => 'danger']);

            DB::table('service_request_statuses')
                ->where('color', 'amber')
                ->update(['color' => 'warning']);

            DB::table('service_request_statuses')
                ->where('color', 'green')
                ->update(['color' => 'success']);

            DB::table('service_request_statuses')
                ->where('color', 'blue')
                ->update(['color' => 'info']);

            DB::statement('alter table service_request_statuses enable trigger prevent_modification_of_system_protected_rows');
        });
    }
};
