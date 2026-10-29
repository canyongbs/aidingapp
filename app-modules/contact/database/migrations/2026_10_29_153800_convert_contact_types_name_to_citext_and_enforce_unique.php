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

use App\Features\ContactTypeNameUniquenessFeature;
use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /*
     * TODO: Cleanup Task (contact-type-name-uniqueness): after ContactTypeNameUniquenessFeature is removed:
     * - remove the `FixesDuplicateNames` trait, the `$chunkSize` and `$usesSoftDeletes` properties, the `MAX_LENGTH` constant, the `buildDeduplicatedValue()` / `existingValueMatchPatterns()` overrides, and the `fixDuplicates()` call
     * - keep the table lock, the citext column conversion and the unique index, they are permanent
     */
    use FixesDuplicateNames;

    private const int MAX_LENGTH = 255;

    private string $table = 'contact_types';

    private string $column = 'name';

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = true;

    public function up(): void
    {
        DB::transaction(function (): void {
            // Blocks concurrent writes between the duplicate repair and the unique index creation.
            DB::statement("LOCK TABLE {$this->table} IN ACCESS EXCLUSIVE MODE");

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table): void {
                $table->uniqueIndex($this->column, "{$this->table}_name_unique")
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });

            ContactTypeNameUniquenessFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ContactTypeNameUniquenessFeature::deactivate();

            DB::statement("DROP INDEX IF EXISTS {$this->table}_name_unique");

            // Renamed duplicates are intentionally kept: stripping the suffix cannot tell a repaired name from a legitimate one such as "Type-2", nor restore truncated characters.
            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
        });
    }

    /**
     * Truncates the base name so the suffixed value never exceeds the column length.
     */
    protected function buildDeduplicatedValue(string $originalValue, int $counter): string
    {
        $suffix = "-{$counter}";

        return mb_substr($originalValue, 0, self::MAX_LENGTH - mb_strlen($suffix)) . $suffix;
    }

    /**
     * Truncated candidates no longer share the full base value as a prefix, so long names are matched on a shorter prefix.
     *
     * @return array{0: string, 1: string}
     */
    protected function existingValueMatchPatterns(string $baseValue): array
    {
        $lower = Str::lower($baseValue);
        $prefixLength = self::MAX_LENGTH - 10;

        if (mb_strlen($lower) <= $prefixLength) {
            return [$lower, addcslashes($lower, '\\%_') . '-%'];
        }

        return [$lower, addcslashes(mb_substr($lower, 0, $prefixLength), '\\%_') . '%'];
    }
};
