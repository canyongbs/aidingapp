<?php

use App\Features\EmailAutomaticCreationFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_types', function (Blueprint $table) {
                $table->string('email_automatic_creation_contact_create_condition')->default('if_eligible');
            });

            EmailAutomaticCreationFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            EmailAutomaticCreationFeature::deactivate();

            Schema::table('service_request_types', function (Blueprint $table) {
                $table->dropColumn('email_automatic_creation_contact_create_condition');
            });
        });
    }
};
