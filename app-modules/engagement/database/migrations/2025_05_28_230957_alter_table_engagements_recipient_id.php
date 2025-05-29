<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagements ALTER COLUMN recipient_id SET DATA TYPE UUID USING recipient_id::uuid');

        DB::statement('ALTER TABLE engagements ADD CONSTRAINT contacts_id_recipient_id FOREIGN KEY (recipient_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagements ALTER COLUMN recipient_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE engagements DROP CONSTRAINT contacts_id_recipient_id;');

        DB::commit();
    }
};
