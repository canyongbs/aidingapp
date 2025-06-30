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

            $table->string('email_automatic_creation_domain')
                ->nullable();

            $table->boolean('is_email_automatic_creation_contact_create_enabled')
                ->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_request_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_email_automatic_creation_enabled',
                'email_automatic_creation_domain',
                'is_email_automatic_creation_contact_create_enabled',
            ]);
        });
    }
};
