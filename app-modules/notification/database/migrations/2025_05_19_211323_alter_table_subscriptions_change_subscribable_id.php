<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE subscriptions ALTER COLUMN subscribable_id SET DATA TYPE UUID USING subscribable_id::uuid');

        DB::statement('ALTER TABLE subscriptions ADD CONSTRAINT contacts_id_subscribable_id FOREIGN KEY (subscribable_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE subscriptions ALTER COLUMN subscribable_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE subscriptions DROP CONSTRAINT contacts_id_subscribable_id;');

        DB::commit();
    }
};
