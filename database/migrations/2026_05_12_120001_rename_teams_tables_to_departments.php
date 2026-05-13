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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * @var array<string, string>
     */
    private array $pivotTables = [
        'project_manager_teams' => 'project_manager_departments',
        'project_auditor_teams' => 'project_auditor_departments',
        'service_request_type_manager_teams' => 'service_request_type_manager_departments',
        'service_request_type_auditor_teams' => 'service_request_type_auditor_departments',
        'service_monitoring_target_team' => 'service_monitoring_target_department',
        'confidential_task_teams' => 'confidential_task_departments',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $this->dropTeamForeignKeys();

            Schema::rename('teams', 'departments');

            foreach ($this->pivotTables as $old => $new) {
                Schema::rename($old, $new);
            }

            $this->renameConstraintsAndIndexesForward();

            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_team_id', 'assigned_department_id'));

            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            }

            $this->addDepartmentForeignKeys();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->dropDepartmentForeignKeys();

            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));
            }

            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_department_id', 'assigned_team_id'));
            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));

            $this->renameConstraintsAndIndexesBackward();

            foreach (array_reverse($this->pivotTables, preserve_keys: true) as $old => $new) {
                Schema::rename($new, $old);
            }

            Schema::rename('departments', 'teams');

            $this->addTeamForeignKeys();
        });
    }

    private function dropTeamForeignKeys(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['team_id']));
        Schema::table('advisories', fn (Blueprint $table) => $table->dropForeign(['assigned_team_id']));

        foreach (array_keys($this->pivotTables) as $old) {
            Schema::table($old, fn (Blueprint $table) => $table->dropForeign(['team_id']));
        }
    }

    private function addDepartmentForeignKeys(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments');
        });

        Schema::table('advisories', function (Blueprint $table) {
            $table->foreign('assigned_department_id')->references('id')->on('departments');
        });

        foreach ($this->pivotTables as $new) {
            Schema::table($new, function (Blueprint $table) {
                $table->foreign('department_id')->references('id')->on('departments')->cascadeOnDelete();
            });
        }
    }

    private function dropDepartmentForeignKeys(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['department_id']));
        Schema::table('advisories', fn (Blueprint $table) => $table->dropForeign(['assigned_department_id']));

        foreach ($this->pivotTables as $new) {
            Schema::table($new, fn (Blueprint $table) => $table->dropForeign(['department_id']));
        }
    }

    private function addTeamForeignKeys(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
        });

        Schema::table('advisories', function (Blueprint $table) {
            $table->foreign('assigned_team_id')->references('id')->on('teams');
        });

        foreach (array_keys($this->pivotTables) as $old) {
            Schema::table($old, function (Blueprint $table) {
                $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            });
        }
    }

    /**
     * Renames Postgres-internal index and constraint names so they reflect the new table name.
     * IF EXISTS on index renames so the migration tolerates schemas where a particular
     * auto-generated index never got created (Laravel's `constrained()` adds the FK
     * constraint but not a separate column index).
     */
    private function renameConstraintsAndIndexesForward(): void
    {
        DB::statement('ALTER INDEX IF EXISTS teams_pkey RENAME TO departments_pkey');
        DB::statement('ALTER INDEX IF EXISTS teams_name_unique RENAME TO departments_name_unique');
        DB::statement('ALTER INDEX IF EXISTS teams_division_id_index RENAME TO departments_division_id_index');
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT teams_division_id_foreign TO departments_division_id_foreign');

        DB::statement('ALTER INDEX IF EXISTS project_manager_teams_pkey RENAME TO project_manager_departments_pkey');
        DB::statement('ALTER INDEX IF EXISTS project_auditor_teams_pkey RENAME TO project_auditor_departments_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_request_type_manager_teams_pkey RENAME TO service_request_type_manager_departments_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_request_type_auditor_teams_pkey RENAME TO service_request_type_auditor_departments_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_monitoring_target_team_pkey RENAME TO service_monitoring_target_department_pkey');
        DB::statement('ALTER INDEX IF EXISTS confidential_task_teams_pkey RENAME TO confidential_task_departments_pkey');

        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_teams_project_id_foreign TO project_manager_departments_project_id_foreign');
        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_teams_project_id_foreign TO project_auditor_departments_project_id_foreign');
        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_manager_teams_service_request_type_id_foreign TO service_request_type_manager_departments_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditor_teams_service_request_type_id_foreign TO service_request_type_auditor_departments_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_team_service_monitoring_target_id_foreign TO service_monitoring_target_department_service_monitoring_target_id_foreign');
        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_teams_task_id_foreign TO confidential_task_departments_task_id_foreign');
    }

    private function renameConstraintsAndIndexesBackward(): void
    {
        DB::statement('ALTER INDEX IF EXISTS departments_pkey RENAME TO teams_pkey');
        DB::statement('ALTER INDEX IF EXISTS departments_name_unique RENAME TO teams_name_unique');
        DB::statement('ALTER INDEX IF EXISTS departments_division_id_index RENAME TO teams_division_id_index');
        DB::statement('ALTER TABLE teams RENAME CONSTRAINT departments_division_id_foreign TO teams_division_id_foreign');

        DB::statement('ALTER INDEX IF EXISTS project_manager_departments_pkey RENAME TO project_manager_teams_pkey');
        DB::statement('ALTER INDEX IF EXISTS project_auditor_departments_pkey RENAME TO project_auditor_teams_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_request_type_manager_departments_pkey RENAME TO service_request_type_manager_teams_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_request_type_auditor_departments_pkey RENAME TO service_request_type_auditor_teams_pkey');
        DB::statement('ALTER INDEX IF EXISTS service_monitoring_target_department_pkey RENAME TO service_monitoring_target_team_pkey');
        DB::statement('ALTER INDEX IF EXISTS confidential_task_departments_pkey RENAME TO confidential_task_teams_pkey');

        DB::statement('ALTER TABLE project_manager_teams RENAME CONSTRAINT project_manager_departments_project_id_foreign TO project_manager_teams_project_id_foreign');
        DB::statement('ALTER TABLE project_auditor_teams RENAME CONSTRAINT project_auditor_departments_project_id_foreign TO project_auditor_teams_project_id_foreign');
        DB::statement('ALTER TABLE service_request_type_manager_teams RENAME CONSTRAINT service_request_type_manager_departments_service_request_type_id_foreign TO service_request_type_manager_teams_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_auditor_teams RENAME CONSTRAINT service_request_type_auditor_departments_service_request_type_id_foreign TO service_request_type_auditor_teams_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_monitoring_target_team RENAME CONSTRAINT service_monitoring_target_department_service_monitoring_target_id_foreign TO service_monitoring_target_team_service_monitoring_target_id_foreign');
        DB::statement('ALTER TABLE confidential_task_teams RENAME CONSTRAINT confidential_task_departments_task_id_foreign TO confidential_task_teams_task_id_foreign');
    }
};
