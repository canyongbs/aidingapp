<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->boolean('is_email_automatic_creation_enabled')
                ->default(false);

            $table->boolean('is_email_automatic_creation_contact_create_enabled')
                ->default(false);

            $table->foreignUuid('email_automatic_creation_priority_id')
                ->nullable()
                ->constrained('service_request_priorities');

            $table->string('email_automatic_creation_bcc')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_email_automatic_creation_enabled',
                'is_email_automatic_creation_contact_create_enabled',
                'email_automatic_creation_priority_id',
                'email_automatic_creation_bcc',
            ]);
        });
    }
};
