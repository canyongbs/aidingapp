<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS citext');
    }

    public function down(): void
    {
        DB::statement('DROP EXTENSION IF EXISTS citext');
    }
};
