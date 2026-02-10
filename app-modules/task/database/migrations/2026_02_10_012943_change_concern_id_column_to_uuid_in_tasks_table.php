<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE tasks
            ALTER COLUMN concern_id TYPE uuid
            USING concern_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE tasks
            ALTER COLUMN concern_id TYPE varchar(255)
            USING concern_id::varchar(255)
        ');
    }
};
