<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tenant_service_request_type_domains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->uuid('service_request_type_id');
            $table->string('domain');
            $table->timestamps();

            $table->uniqueIndex(['tenant_id', 'service_request_type_id']);
            $table->uniqueIndex(['tenant_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_service_request_type_domains');
    }
};
