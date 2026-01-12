<?php

use App\Features\JobTitleFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('job_title')->nullable();
            });

            JobTitleFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            JobTitleFeature::deactivate();

            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('job_title');
            });
        });
    }
};
