<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_type_managers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->uuidMorphs('managerable');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_type_managers', function (Blueprint $table) {
            $table->dropMorphs('managerable');
            $table->foreignUuid('team_id')->constrained('teams')->cascadeOnDelete();
        });
    }
};
