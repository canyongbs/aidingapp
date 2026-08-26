<?php

use App\Features\PasswordFormFieldFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->text('secret_key')->nullable();
            });

            PasswordFormFieldFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            PasswordFormFieldFeature::deactivate();

            Schema::table('service_requests', function (Blueprint $table) {
                $table->dropColumn('secret_key');
            });
        });
    }
};
