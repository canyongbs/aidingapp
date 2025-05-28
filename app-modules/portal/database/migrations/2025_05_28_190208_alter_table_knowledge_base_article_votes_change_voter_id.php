<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_article_votes ALTER COLUMN voter_id SET DATA TYPE UUID USING voter_id::uuid');

        DB::statement('ALTER TABLE knowledge_base_article_votes ADD CONSTRAINT contacts_id_voter_id FOREIGN KEY (voter_id) REFERENCES contacts (id) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE knowledge_base_article_votes ALTER COLUMN voter_id SET DATA TYPE varchar(255)');

        DB::statement('ALTER TABLE knowledge_base_article_votes DROP CONSTRAINT contacts_id_voter_id;');

        DB::commit();
    }
};
