<?php

use App\Features\GroupManagementFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('group_user', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('group_id')->constrained('groups')->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->uniqueIndex(['group_id', 'user_id']);
            });

            GroupManagementFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            GroupManagementFeature::deactivate();

            Schema::dropIfExists('group_user');
        });
    }
};
