<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    private string $table = 'service_request_statuses';

    private string $column = 'name';

    private string $trigger = 'prevent_modification_of_system_protected_rows';

    public function up(): void
    {
        DB::transaction(function () {
            /*
             * TODO: KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature cleanup — once this migration has run in all environments:
             * - Remove the trigger disable/enable below and the fixDuplicates() call + helper methods
             * - Keep the citext column conversion and the unique index — those are permanent
             */
            DB::statement("ALTER TABLE {$this->table} DISABLE TRIGGER {$this->trigger}");

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ENABLE TRIGGER {$this->trigger}");

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->uniqueIndex($this->column, 'service_request_statuses_name_unique')
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS service_request_statuses_name_unique');

        DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
    }

    /**
     * Resolve every case-insensitive duplicate name so the unique index can be created.
     *
     * Within a duplicate name group:
     *  - Rows sharing the same classification are merged: keep one (preferring the system
     *    protected row, then the latest), reassign its service requests/assignments and history
     *    references, soft delete the rest.
     *  - Rows that share the name but differ in classification cannot be merged, so all but the
     *    surviving "primary" row are suffix-renamed (e.g. "New-2") for the customer to resolve.
     */
    private function fixDuplicates(): void
    {
        $duplicateNames = DB::table($this->table)
            ->whereNull('deleted_at')
            ->selectRaw("LOWER({$this->column}) as lower_name")
            ->groupByRaw("LOWER({$this->column})")
            ->havingRaw('COUNT(*) > 1')
            ->pluck('lower_name');

        foreach ($duplicateNames as $duplicateName) {
            $rows = DB::table($this->table)
                ->whereNull('deleted_at')
                ->whereRaw("LOWER({$this->column}) = ?", [$duplicateName])
                ->orderByRaw('is_system_protected DESC')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get(['id', 'name', 'classification', 'is_system_protected', 'created_at']);

            $survivors = $this->mergeWithinClassifications($rows);

            $this->renameAcrossClassifications($survivors);
        }
    }

    /**
     * @param  Collection<int, stdClass>  $rows
     *
     * @return Collection<int, stdClass>
     */
    private function mergeWithinClassifications(Collection $rows): Collection
    {
        $survivors = collect();

        foreach ($rows->groupBy('classification') as $group) {
            /** @var stdClass $keeper */
            $keeper = $group->first();

            $survivors->push($keeper);

            /** @var array<int, string> $loserIds */
            $loserIds = $group->slice(1)->pluck('id')->all();

            if (empty($loserIds)) {
                continue;
            }

            DB::table('service_requests')
                ->whereIn('status_id', $loserIds)
                ->update(['status_id' => $keeper->id]);

            DB::table('service_request_assignments')
                ->whereIn('service_request_status_id', $loserIds)
                ->update(['service_request_status_id' => $keeper->id]);

            $this->rewriteHistoryStatus($loserIds, $keeper->id);

            DB::table($this->table)
                ->whereIn('id', $loserIds)
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return $survivors;
    }

    /**
     * @param  array<int, string>  $loserIds
     */
    private function rewriteHistoryStatus(array $loserIds, string $keeperId): void
    {
        $placeholders = implode(',', array_fill(0, count($loserIds), '?'));

        foreach (['original_values', 'new_values'] as $column) {
            DB::statement(
                "UPDATE service_request_histories
                 SET {$column} = jsonb_set({$column}::jsonb, '{status_id}', to_jsonb(?::text))::json
                 WHERE {$column}->>'status_id' IN ({$placeholders})",
                [$keeperId, ...$loserIds],
            );
        }
    }

    /**
     * @param  Collection<int, stdClass>  $survivors
     */
    private function renameAcrossClassifications(Collection $survivors): void
    {
        if ($survivors->count() <= 1) {
            return;
        }

        foreach ($survivors->slice(1) as $survivor) {
            DB::table($this->table)
                ->where('id', $survivor->id)
                ->update([
                    'name' => $this->generateUniqueName($survivor->name),
                    'updated_at' => now(),
                ]);
        }
    }

    private function generateUniqueName(string $name): string
    {
        $counter = 2;

        do {
            $candidate = "{$name}-{$counter}";
            $counter++;

            $exists = DB::table($this->table)
                ->whereNull('deleted_at')
                ->whereRaw("LOWER({$this->column}) = ?", [strtolower($candidate)])
                ->exists();
        } while ($exists);

        return $candidate;
    }
};
