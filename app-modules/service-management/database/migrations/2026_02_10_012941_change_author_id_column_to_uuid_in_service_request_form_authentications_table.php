<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('
            ALTER TABLE service_request_form_authentications
            ALTER COLUMN author_id TYPE uuid
            USING author_id::uuid
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE service_request_form_authentications
            ALTER COLUMN author_id TYPE varchar(255)
            USING author_id::varchar(255)
        ');
    }
};
