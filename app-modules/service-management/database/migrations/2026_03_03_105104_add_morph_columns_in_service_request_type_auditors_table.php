<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_request_type_auditors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->uuidMorphs('auditorable');
        });
    }

    public function down(): void
    {
        Schema::table('service_request_type_auditors', function (Blueprint $table) {
            $table->dropMorphs('auditorable');
            $table->foreignUuid('team_id')->constrained('teams')->cascadeOnDelete();
        });
    }
};
