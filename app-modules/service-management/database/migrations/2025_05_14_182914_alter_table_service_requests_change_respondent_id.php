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

        DB::statement('ALTER TABLE service_requests ALTER COLUMN respondent_id SET DATA TYPE UUID USING respondent_id::uuid');

        DB::statement('ALTER TABLE service_requests ADD CONSTRAINT contacts_id_respondent_id FOREIGN KEY (respondent_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_requests ALTER COLUMN respondent_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE service_requests DROP CONSTRAINT contacts_id_respondent_id;');

        DB::commit();
    }
};
