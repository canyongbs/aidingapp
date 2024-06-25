<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('service_request_types')->whereNotNull('deleted_at')->orderBy('id')->lazy()->each(function (object $serviceRequestType) {
            DB::table('service_request_priorities')->where('type_id', $serviceRequestType->id)->update(['deleted_at' => now()]);
        });
    }

    public function down(): void
    {
        DB::table('service_request_types')->whereNotNull('deleted_at')->orderBy('id')->lazy()->each(function (object $serviceRequestType) {
            DB::table('service_request_priorities')->where('type_id', $serviceRequestType->id)->update(['deleted_at' => null]);
        });
    }
};
