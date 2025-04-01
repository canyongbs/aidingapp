<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historical_service_monitorings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->integer('response');
            $table->foreignUuid('service_monitoring_target_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historical_service_monitorings');
    }
};
