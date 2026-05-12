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

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    use CanModifyPermissions;

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

    /**
     * @var array<string, string>
     */
    private array $permissionRenames = [
        'team.view-any' => 'department.view-any',
        'team.create' => 'department.create',
        'team.*.view' => 'department.*.view',
        'team.*.update' => 'department.*.update',
        'team.*.delete' => 'department.*.delete',
        'team.*.restore' => 'department.*.restore',
        'team.*.force-delete' => 'department.*.force-delete',
    ];

    /**
     * @var array<string>
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $this->dropTeamForeignKeys();

            Schema::rename('teams', 'departments');

            foreach ($this->pivotTables as $old => $new) {
                Schema::rename($old, $new);
            }

            $this->renameConstraintsAndIndexes(forward: true);

            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_team_id', 'assigned_department_id'));

            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            }

            $this->addDepartmentForeignKeys();

            DB::table('audits')
                ->where('auditable_type', 'team')
                ->update(['auditable_type' => 'department']);

            foreach ($this->guards as $guard) {
                $this->renamePermissions($this->permissionRenames, $guard);
            }

            $this->renamePermissionGroups(['Team' => 'Department']);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->renamePermissionGroups(['Department' => 'Team']);

            foreach ($this->guards as $guard) {
                $this->renamePermissions(array_flip($this->permissionRenames), $guard);
            }

            DB::table('audits')
                ->where('auditable_type', 'department')
                ->update(['auditable_type' => 'team']);

            $this->dropDepartmentForeignKeys();

            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));
            }

            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_department_id', 'assigned_team_id'));
            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));

            $this->renameConstraintsAndIndexes(forward: false);

            foreach (array_reverse($this->pivotTables, preserve_keys: true) as $old => $new) {
                Schema::rename($new, $old);
            }

            Schema::rename('departments', 'teams');

            $this->addTeamForeignKeys();

            app(PermissionRegistrar::class)->forgetCachedPermissions();
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

    private function renameConstraintsAndIndexes(bool $forward): void
    {
        $renames = [
            ['ALTER INDEX %s RENAME TO %s', 'teams_pkey', 'departments_pkey'],
            ['ALTER INDEX %s RENAME TO %s', 'teams_name_unique', 'departments_name_unique'],
            ['ALTER INDEX %s RENAME TO %s', 'teams_division_id_index', 'departments_division_id_index'],
            ['ALTER TABLE departments RENAME CONSTRAINT %s TO %s', 'teams_division_id_foreign', 'departments_division_id_foreign'],
        ];

        foreach ($this->pivotTables as $old => $new) {
            $renames[] = ['ALTER INDEX %s RENAME TO %s', "{$old}_pkey", "{$new}_pkey"];
        }

        $pivotForeignKeys = [
            'project_manager_teams' => ['project_manager_departments', 'project_id'],
            'project_auditor_teams' => ['project_auditor_departments', 'project_id'],
            'service_request_type_manager_teams' => ['service_request_type_manager_departments', 'service_request_type_id'],
            'service_request_type_auditor_teams' => ['service_request_type_auditor_departments', 'service_request_type_id'],
            'service_monitoring_target_team' => ['service_monitoring_target_department', 'service_monitoring_target_id'],
            'confidential_task_teams' => ['confidential_task_departments', 'task_id'],
        ];

        foreach ($pivotForeignKeys as $oldTable => [$newTable, $column]) {
            $renames[] = [
                "ALTER TABLE {$newTable} RENAME CONSTRAINT %s TO %s",
                "{$oldTable}_{$column}_foreign",
                "{$newTable}_{$column}_foreign",
            ];
        }

        foreach ($renames as [$template, $old, $new]) {
            if ($forward) {
                DB::statement(sprintf($template, $old, $new));
            } else {
                DB::statement(sprintf($template, $new, $old));
            }
        }
    }
};
