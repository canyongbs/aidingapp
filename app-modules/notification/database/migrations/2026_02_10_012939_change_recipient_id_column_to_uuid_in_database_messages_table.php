<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE database_messages
            ALTER COLUMN recipient_id TYPE uuid
            USING recipient_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE database_messages
            ALTER COLUMN recipient_id TYPE varchar(255)
            USING recipient_id::varchar(255)
        ');
    }
};
