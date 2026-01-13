<?php

use App\Features\RemoveAssignedToFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feature', function (Blueprint $table) {
            RemoveAssignedToFeature::active();
        });
    }

    public function down(): void
    {
        Schema::table('feature', function (Blueprint $table) {
            RemoveAssignedToFeature::deactivate();
        });
    }
};
