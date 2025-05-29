<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagement_responses ALTER COLUMN sender_id SET DATA TYPE UUID USING sender_id::uuid');

        DB::statement('ALTER TABLE engagement_responses ADD CONSTRAINT contacts_id_sender_id FOREIGN KEY (sender_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagement_responses ALTER COLUMN sender_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE engagement_responses DROP CONSTRAINT contacts_id_sender_id;');

        DB::commit();
    }
};
