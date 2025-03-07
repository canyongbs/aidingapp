<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('engagements')->truncate();
        DB::table('engagement_batches')->truncate();
        DB::table('engagement_responses')->truncate();
    }

    public function down(): void
    {
        // There is no way to undo this migration
    }
};
