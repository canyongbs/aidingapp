<?php

use App\Features\ProjectArchivingFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('projects', function (Blueprint $table) {
                $table->timestamp('archived_at')->nullable();
            });

            ProjectArchivingFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ProjectArchivingFeature::deactivate();

            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('archived_at');
            });
        });
    }
};
