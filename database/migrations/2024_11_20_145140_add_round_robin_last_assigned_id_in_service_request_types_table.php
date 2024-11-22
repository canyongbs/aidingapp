<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->foreignUuid('round_robin_last_assigned_id')
                ->nullable()
                ->references('id')
                ->on('service_request_type_managers');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn('round_robin_last_assigned_id');
        });
    }
};
