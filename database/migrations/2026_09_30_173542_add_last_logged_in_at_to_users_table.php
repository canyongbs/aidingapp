<?php

use App\Features\LastLoggedInFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_logged_in_at')->nullable();
            });

            LastLoggedInFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            LastLoggedInFeature::deactivate();

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_logged_in_at');
            });
        });
    }
};
