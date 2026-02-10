<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE personal_access_tokens
            ALTER COLUMN tokenable_id TYPE uuid
            USING tokenable_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE personal_access_tokens
            ALTER COLUMN tokenable_id TYPE varchar(255)
            USING tokenable_id::varchar(255)
        ');
    }
};
