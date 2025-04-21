<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_type_email_templates', function (Blueprint $table) {
            $table->string('role')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('service_request_type_email_templates', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
