<?php

use App\Features\AutomatedStatusChangeOnAssignmentFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_types', function (Blueprint $table) {
                if (! Schema::hasColumn('service_request_types', 'is_automated_status_change_enabled')) {
                    $table->boolean('is_automated_status_change_enabled')->default(false);
                }

                if (! Schema::hasColumn('service_request_types', 'automated_status_id')) {
                    $table->foreignUuid('automated_status_id')->nullable()->constrained('service_request_statuses')->nullOnDelete();
                }
            });

            AutomatedStatusChangeOnAssignmentFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AutomatedStatusChangeOnAssignmentFeature::deactivate();

            Schema::table('service_request_types', function (Blueprint $table) {
                if (Schema::hasColumn('service_request_types', 'automated_status_id')) {
                    $table->dropConstrainedForeignId('automated_status_id');
                }

                if (Schema::hasColumn('service_request_types', 'is_automated_status_change_enabled')) {
                    $table->dropColumn('is_automated_status_change_enabled');
                }
            });
        });
    }
};
