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

use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    use FixesDuplicateNames;

    private string $table = 'contacts';

    private string $column = 'email';

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = true;

    public function up(): void
    {
        DB::transaction(function () {
            /*
             * TODO: After feature is stable:
             * - Remove the $this->fixDuplicates() call below
             * - Remove the revertDuplicates() call in down()
             * - Remove the $chunkSize and $usesSoftDeletes properties
             * - Remove the FixesDuplicateNames trait and the email-specific override methods below
             */
            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->uniqueIndex($this->column, 'contacts_email_unique')
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('DROP INDEX IF EXISTS contacts_email_unique');

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");

            $this->revertDuplicates();
        });
    }

    protected function ignoresNullValues(): bool
    {
        return true;
    }

    protected function orderDuplicateRecords(Builder $query): Builder
    {
        return $query
            ->orderByRaw('user_id IS NULL')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function existingValueMatchPatterns(string $baseValue): array
    {
        $lower = Str::lower($baseValue);

        [$local, $domain] = array_pad(explode('@', $lower, 2), 2, '');

        return [$lower, $domain === '' ? "{$local}+%" : "{$local}+%@{$domain}"];
    }

    protected function buildDeduplicatedValue(string $originalValue, int $counter): string
    {
        // Rewrite duplicates using plus-addressing (john@example.com -> john+2@example.com)
        // so the address stays valid and deliverable to the same inbox.
        [$local, $domain] = array_pad(explode('@', $originalValue, 2), 2, '');

        return $domain === '' ? "{$local}+{$counter}" : "{$local}+{$counter}@{$domain}";
    }

    protected function deduplicatedValuePattern(): string
    {
        return '\+[0-9]+(@|$)';
    }

    protected function stripDeduplicatedSuffix(string $value): string
    {
        return preg_replace('/\+\d+(?=@|$)/', '', $value) ?? $value;
    }
};
