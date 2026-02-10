<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE engagement_responses
            ALTER COLUMN sender_id TYPE uuid
            USING sender_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE engagement_responses
            ALTER COLUMN sender_id TYPE varchar(255)
            USING sender_id::varchar(255)
        ');
    }
};
