<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE asset_check_outs
            ALTER COLUMN checked_out_by_id TYPE uuid
            USING checked_out_by_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE asset_check_outs
            ALTER COLUMN checked_out_by_id TYPE varchar(255)
            USING checked_out_by_id::varchar(255)
        ');
    }
};
