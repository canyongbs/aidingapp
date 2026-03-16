<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;

return new class () extends Migration {
    private string $table = 'service_request_priorities';

    private string $column = 'name';

    private int $chunkSize = 500;

    public function up(): void
    {
        DB::transaction(function () {
            /*
             * TODO: After feature is stable:
             * - Remove the $this->fixDuplicates() call below
             * - Remove the $chunkSize property
             */
            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->unique([$this->column, 'type_id']);
            });
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropUnique([$this->column, 'type_id']);
        });

        DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
    }

    private function fixDuplicates(): void
    {
        $duplicates = DB::table($this->table)
            ->select('type_id', DB::raw("LOWER({$this->column}) as lower_name"))
            ->whereNull('deleted_at')
            ->groupBy('type_id', DB::raw("LOWER({$this->column})"))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        foreach ($duplicates as $duplicate) {
            $records = DB::table($this->table)
                ->select('id', $this->column, 'created_at')
                ->whereNull('deleted_at')
                ->where('type_id', $duplicate->type_id)
                ->whereRaw("LOWER({$this->column}) = ?", [$duplicate->lower_name])
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($records->count() <= 1) {
                continue;
            }

            $updates = [];
            $counter = 2;

            foreach ($records->skip(1) as $record) {
                /** @var string $originalName */
                $originalName = $record->{$this->column};
                $newName = "{$originalName}-{$counter}";

                while (DB::table($this->table)
                    ->whereNull('deleted_at')
                    ->where('type_id', $duplicate->type_id)
                    ->whereRaw("LOWER({$this->column}) = ?", [strtolower($newName)])
                    ->exists()) {
                    $counter++;
                    $newName = "{$originalName}-{$counter}";
                }

                $updates[$record->id] = $newName;
                $counter++;

                if (count($updates) >= $this->chunkSize) {
                    $this->batchUpdate($updates);
                    $updates = [];
                }
            }

            if (! empty($updates)) {
                $this->batchUpdate($updates);
            }
        }
    }

    /**
     * @param array<string, string> $updates
     */
    private function batchUpdate(array $updates): void
    {
        $cases = [];
        $ids = [];
        $bindings = [];

        foreach ($updates as $id => $newName) {
            $cases[] = 'WHEN id = ? THEN ?';
            $bindings[] = $id;
            $bindings[] = $newName;
            $ids[] = $id;
        }

        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        $sql = "UPDATE {$this->table} SET {$this->column} = CASE " . implode(' ', $cases) . " END WHERE id IN ({$idPlaceholders})";

        DB::statement($sql, $bindings);
    }
};
