<?php

use App\Features\ContactChangesFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::rename('contact_statuses', 'contact_types');

            Schema::table('contacts', function (Blueprint $table) {
                $table->renameColumn('status_id', 'type_id');
            });

            ContactChangesFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ContactChangesFeature::deactivate();

            Schema::table('contacts', function (Blueprint $table) {
                $table->renameColumn('type_id', 'status_id');
            });

            Schema::rename('contact_types', 'contact_statuses');
        });
    }
};
