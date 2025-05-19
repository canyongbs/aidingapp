<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE alerts ALTER COLUMN concern_id SET DATA TYPE UUID USING concern_id::uuid');

        DB::statement('ALTER TABLE alerts ADD CONSTRAINT contacts_id_concern_id FOREIGN KEY (concern_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE alerts ALTER COLUMN concern_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE alerts DROP CONSTRAINT contacts_id_concern_id;');

        DB::commit();
    }
};
