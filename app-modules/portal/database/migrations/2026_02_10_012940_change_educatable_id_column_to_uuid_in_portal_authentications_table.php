<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE portal_authentications
            ALTER COLUMN educatable_id TYPE uuid
            USING educatable_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE portal_authentications
            ALTER COLUMN educatable_id TYPE varchar(255)
            USING educatable_id::varchar(255)
        ');
    }
};
