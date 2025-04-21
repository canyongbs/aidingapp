<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_type_email_templates', function (Blueprint $table) {
            $table->dropUnique('service_request_type_email_templates_service_request_type_id_ty');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_type_email_templates', function (Blueprint $table) {
            $table->unique(['service_request_type_id', 'type']);
        });
    }
};
