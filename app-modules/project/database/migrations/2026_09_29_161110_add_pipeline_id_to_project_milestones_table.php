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

use App\Features\AssociateMilestoneWithActivePipelineFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            // A milestone must always belong to a pipeline. The column is added nullable so it can
            // be back-filled, then tightened to NOT NULL below. Deleting a pipeline deletes its
            // milestones (cascade), since a milestone cannot exist without one.
            Schema::table('project_milestones', function (Blueprint $table) {
                $table->foreignUuid('pipeline_id')->nullable()->index()->constrained('pipelines')->cascadeOnDelete();
            });

            // Associate every existing milestone with its project's active pipeline. There is no
            // persisted "active" pipeline, so the first (oldest, non-archived) pipeline of the
            // project is used, matching how the application resolves the default active pipeline.
            DB::table('project_milestones')
                ->whereNull('pipeline_id')
                ->update([
                    'pipeline_id' => DB::raw(<<<'SQL'
                        (
                            select pipelines.id
                            from pipelines
                            where pipelines.project_id = project_milestones.project_id
                                and pipelines.archived_at is null
                            order by pipelines.created_at asc, pipelines.id asc
                            limit 1
                        )
                        SQL),
                ]);

            // A milestone now belongs to one pipeline, so unlink tasks in any other pipeline from it.
            DB::table('pipeline_entries')
                ->join('pipeline_stages', 'pipeline_stages.id', '=', 'pipeline_entries.pipeline_stage_id')
                ->join('project_milestones', 'project_milestones.id', '=', 'pipeline_entries.project_milestone_id')
                ->whereRaw('project_milestones.pipeline_id is distinct from pipeline_stages.pipeline_id')
                ->update(['project_milestone_id' => null]);

            // Any milestone still without a pipeline belongs to a project that has no (non-archived)
            // pipeline to associate it with. Per product decision these milestones are not needed, so
            // they are removed.
            DB::table('project_milestones')
                ->whereNull('pipeline_id')
                ->delete();

            // Now that every remaining milestone has a pipeline, enforce the relationship at the
            // database level so a milestone can never be persisted without one.
            Schema::table('project_milestones', function (Blueprint $table) {
                $table->foreignUuid('pipeline_id')->nullable(false)->change();
            });

            // TODO: Cleanup Task (associate-milestone-with-active-pipeline): this permanent
            // migration cannot be deleted, so when the flag is removed drop these activate()/
            // deactivate() calls and the AssociateMilestoneWithActivePipelineFeature import —
            // otherwise it references a deleted class and breaks migrate/tests on fresh databases.
            AssociateMilestoneWithActivePipelineFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AssociateMilestoneWithActivePipelineFeature::deactivate();

            Schema::table('project_milestones', function (Blueprint $table) {
                $table->dropForeign(['pipeline_id']);
                $table->dropColumn('pipeline_id');
            });
        });
    }
};
