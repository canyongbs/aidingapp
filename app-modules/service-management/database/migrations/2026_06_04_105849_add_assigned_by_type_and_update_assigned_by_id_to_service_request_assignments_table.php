<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->string('assigned_by_type')->nullable()->after('assigned_by_id');
            });

            DB::table('service_request_assignments')
                ->whereNotNull('assigned_by_id')
                ->update(['assigned_by_type' => (new User())->getMorphClass()]);

            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->dropForeign(['assigned_by_id']);
            });

            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->index(['assigned_by_type', 'assigned_by_id']);
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->dropIndex(['assigned_by_type', 'assigned_by_id']);
            });

            DB::table('service_request_assignments')
                ->whereNotNull('assigned_by_type')
                ->where('assigned_by_type', '!=', 'user')
                ->update(['assigned_by_id' => null]);

            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->foreign('assigned_by_id')->references('id')->on('users');
                $table->dropColumn('assigned_by_type');
            });
        });
    }
};
