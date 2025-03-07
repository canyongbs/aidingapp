<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('outbound_deliverables')
            ->where('related_type', 'engagement_deliverable')
            ->delete();
    }

    public function down(): void
    {
        // There is no way to undo this migration
    }
};
