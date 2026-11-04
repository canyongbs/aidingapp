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

use App\Features\SystemUserNameUniquenessFeature;
use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /*
     * TODO: Cleanup Task (system-user-name-uniqueness): after SystemUserNameUniquenessFeature is removed:
     * - remove the `FixesDuplicateNames` trait, the `$chunkSize` and `$usesSoftDeletes` properties, and the `fixDuplicates()` / `revertDuplicates()` / `reconcileDuplicatesAcrossAllRows()` calls
     * - keep the citext column conversion and the partial unique index, they are permanent
     */
    use FixesDuplicateNames;

    private string $table = 'system_users';

    private string $column = 'name';

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = true;

    public function up(): void
    {
        DB::transaction(function (): void {
            DB::statement('ALTER TABLE system_users DROP CONSTRAINT IF EXISTS system_users_name_unique');

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table): void {
                $table->uniqueIndex($this->column, 'system_users_name_unique')
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });

            SystemUserNameUniquenessFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            SystemUserNameUniquenessFeature::deactivate();

            DB::statement('DROP INDEX IF EXISTS system_users_name_unique');

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");

            $this->revertDuplicates();

            // The partial unique index permits a live row and a soft-deleted row to share an exact
            // name. Reconcile those collisions before restoring the global unique index, otherwise
            // rollback fails with a unique-constraint violation on that supported data.
            $this->reconcileDuplicatesAcrossAllRows();

            Schema::table($this->table, function (Blueprint $table): void {
                $table->unique($this->column, 'system_users_name_unique');
            });
        });
    }
};
