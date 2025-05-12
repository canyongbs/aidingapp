<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->boolean('is_customers_survey_response_email_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_customers_survey_response_email_enabled',
            ]);
        });
    }
};
