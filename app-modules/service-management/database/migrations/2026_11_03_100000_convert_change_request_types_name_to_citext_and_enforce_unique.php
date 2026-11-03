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

use App\Features\ChangeRequestTypeAndStatusNameUniquenessFeature;
use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /*
     * TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): after ChangeRequestTypeAndStatusNameUniquenessFeature is removed:
     * - remove the `FixesDuplicateNames` trait, the `$chunkSize` and `$usesSoftDeletes` properties, the `buildDeduplicatedValue()` / `existingValueMatchPatterns()` overrides, and the `fixDuplicates()` call
     * - keep the citext column conversion and the unique index, they are permanent
     */
    use FixesDuplicateNames;

    private string $table = 'change_request_types';

    private string $column = 'name';

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = true;

    private int $maxLength = 255;

    public function up(): void
    {
        DB::transaction(function (): void {
            // Blocks concurrent writes from code that predates the unique index until the index exists.
            DB::statement("LOCK TABLE {$this->table} IN ACCESS EXCLUSIVE MODE");

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table): void {
                $table->uniqueIndex($this->column, "{$this->table}_name_unique")
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });

            ChangeRequestTypeAndStatusNameUniquenessFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ChangeRequestTypeAndStatusNameUniquenessFeature::deactivate();

            DB::statement("DROP INDEX IF EXISTS {$this->table}_name_unique");

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");

            // Renamed duplicates are intentionally kept: a numeric suffix added here cannot be told apart from one that was part of the original name.
        });
    }

    /**
     * Truncates the original value so the suffixed value still fits in the varchar(255) column.
     */
    protected function buildDeduplicatedValue(string $originalValue, int $counter): string
    {
        $suffix = "-{$counter}";

        return mb_substr($originalValue, 0, $this->maxLength - mb_strlen($suffix)) . $suffix;
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function existingValueMatchPatterns(string $baseValue): array
    {
        $lower = mb_strtolower($baseValue);

        // Truncated candidates no longer start with the full base value, so match on a shorter prefix.
        return [$lower, mb_substr($lower, 0, $this->maxLength - 15) . '%'];
    }
};
