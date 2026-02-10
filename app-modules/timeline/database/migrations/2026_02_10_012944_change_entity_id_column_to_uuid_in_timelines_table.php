<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE timelines
            ALTER COLUMN entity_id TYPE uuid
            USING entity_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE timelines
            ALTER COLUMN entity_id TYPE varchar(255)
            USING entity_id::varchar(255)
        ');
    }
};
