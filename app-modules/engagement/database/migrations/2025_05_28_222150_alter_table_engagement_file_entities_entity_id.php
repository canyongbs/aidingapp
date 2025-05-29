<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagement_file_entities ALTER COLUMN entity_id SET DATA TYPE UUID USING entity_id::uuid');

        DB::statement('ALTER TABLE engagement_file_entities ADD CONSTRAINT contacts_id_entity_id FOREIGN KEY (entity_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE engagement_file_entities ALTER COLUMN entity_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE engagement_file_entities DROP CONSTRAINT contacts_id_entity_id;');

        DB::commit();
    }
};
