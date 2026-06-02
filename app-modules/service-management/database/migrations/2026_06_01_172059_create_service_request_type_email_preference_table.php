<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_request_type_email_preference', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_request_type_id')->constrained('service_request_types');
            $table->boolean('is_enabled')->default(false);
            $table->string('service_request_email_template_type');
            $table->string('service_request_email_template_role');
            $table->string('notification_channel');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['service_request_type_id', 'service_request_email_template_type', 'service_request_email_template_role', 'notification_channel'],
                'srtep_type_role_channel_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_type_email_preference');
    }
};
