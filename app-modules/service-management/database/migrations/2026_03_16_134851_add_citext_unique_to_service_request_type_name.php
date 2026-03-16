<?php

use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    use FixesDuplicateNames;

    private string $table = 'service_request_types';

    private string $column = 'name';

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = false;

    public function up(): void
    {
        DB::transaction(function () {
            /*
             * TODO: After feature is stable:
             * - Remove the $this->fixDuplicates() call below
             * - Remove the revertDuplicates() call in down()
             * - Remove the $chunkSize property
             */
            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->unique($this->column);
            });
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropUnique([$this->column]);
        });

        DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");

        $this->revertDuplicates();
    }
};
