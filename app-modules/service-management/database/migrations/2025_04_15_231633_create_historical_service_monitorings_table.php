<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historical_service_monitorings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->integer('response');
            $table->boolean('succeeded');
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
