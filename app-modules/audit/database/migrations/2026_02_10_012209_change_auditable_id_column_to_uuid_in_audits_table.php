<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE audits
            ALTER COLUMN auditable_id TYPE uuid
            USING auditable_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE audits
            ALTER COLUMN auditable_id TYPE varchar(255)
            USING auditable_id::varchar(255)
        ');
    }
};
