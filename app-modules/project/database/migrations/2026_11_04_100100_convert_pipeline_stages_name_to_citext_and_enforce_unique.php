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

use App\Features\PipelineNameUniquenessFeature;
use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /*
     * TODO: Cleanup Task (pipeline-name-uniqueness): after PipelineNameUniquenessFeature is removed:
     * - remove the `FixesDuplicateNames` trait, the `$chunkSize`, `$usesSoftDeletes` and `$groupByColumns` properties, and the `fixDuplicates()` call
     * - keep the citext column conversion and the unique index, they are permanent
     */
    use FixesDuplicateNames;

    private string $table = 'pipeline_stages';

    private string $column = 'name';

    /** @var array<int, string> */
    private array $groupByColumns = ['pipeline_id'];

    private int $chunkSize = 500;

    private bool $usesSoftDeletes = false;

    public function up(): void
    {
        DB::transaction(function (): void {
            // Block concurrent writes so the repair and the unique constraint operate on a stable set of rows.
            DB::statement("LOCK TABLE {$this->table} IN ACCESS EXCLUSIVE MODE");

            DB::statement('ALTER TABLE pipeline_stages DROP CONSTRAINT IF EXISTS pipeline_stages_name_pipeline_id_unique');

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            // Deferrable so a multi-stage rename (e.g. swapping or shifting names) can be saved in one
            // transaction with `SET CONSTRAINTS ALL DEFERRED` without tripping on intermediate states.
            DB::statement("ALTER TABLE {$this->table} ADD CONSTRAINT pipeline_stages_name_pipeline_id_unique UNIQUE ({$this->column}, pipeline_id) DEFERRABLE INITIALLY IMMEDIATE");

            PipelineNameUniquenessFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            PipelineNameUniquenessFeature::deactivate();

            DB::statement('ALTER TABLE pipeline_stages DROP CONSTRAINT IF EXISTS pipeline_stages_name_pipeline_id_unique');

            DB::statement('DROP INDEX IF EXISTS pipeline_stages_name_pipeline_id_unique');

            // Names rewritten by up() are intentionally kept: stripping numeric suffixes cannot tell them
            // apart from legitimate names and could recreate duplicates.
            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");

            Schema::table($this->table, function (Blueprint $table): void {
                $table->unique([$this->column, 'pipeline_id'], 'pipeline_stages_name_pipeline_id_unique');
            });
        });
    }
};
