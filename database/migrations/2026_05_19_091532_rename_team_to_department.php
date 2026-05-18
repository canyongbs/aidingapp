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

use App\Features\TeamRenameFeature;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            Schema::rename('teams', 'departments');
            foreach ($this->pivotTables as $old => $new) {
                Schema::rename($old, $new);
            }

            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_team_id', 'assigned_department_id'));
            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('team_id', 'department_id'));
            }

            $this->renameConstraintsForward();

            DB::table('audits')
                ->where('auditable_type', 'team')
                ->update(['auditable_type' => 'department']);

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->permissionRenames, $guard);
            });
            $this->renamePermissionGroups(['Team' => 'Department']);

            $this->renameAdvisoryConstraintsForward();

            TeamRenameFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            TeamRenameFeature::deactivate();

            $this->renameAdvisoryConstraintsBackward();

            $this->renamePermissionGroups(['Department' => 'Team']);
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->permissionRenames), $guard);
            });

            DB::table('audits')
                ->where('auditable_type', 'department')
                ->update(['auditable_type' => 'team']);

            $this->renameConstraintsBackward();

            foreach ($this->pivotTables as $new) {
                Schema::table($new, fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));
            }
            Schema::table('advisories', fn (Blueprint $table) => $table->renameColumn('assigned_department_id', 'assigned_team_id'));
            Schema::table('users', fn (Blueprint $table) => $table->renameColumn('department_id', 'team_id'));

            foreach (array_reverse($this->pivotTables, preserve_keys: true) as $old => $new) {
                Schema::rename($new, $old);
            }
            Schema::rename('departments', 'teams');
        });
    }

    private function renameConstraintsForward(): void
    {
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT teams_pkey TO departments_pkey');
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT teams_name_unique TO departments_name_unique');
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT teams_division_id_foreign TO departments_division_id_foreign');

        DB::statement('ALTER TABLE users RENAME CONSTRAINT users_team_id_foreign TO users_department_id_foreign');

        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT incidents_assigned_team_id_foreign TO advisories_assigned_department_id_foreign');

        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_teams_pkey TO project_manager_departments_pkey');
        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_teams_project_id_foreign TO project_manager_departments_project_id_foreign');
        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_teams_team_id_foreign TO project_manager_departments_department_id_foreign');

        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_teams_pkey TO project_auditor_departments_pkey');
        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_teams_project_id_foreign TO project_auditor_departments_project_id_foreign');
        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_teams_team_id_foreign TO project_auditor_departments_department_id_foreign');

        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_team_pkey TO service_monitoring_target_department_pkey');
        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_team_service_monitoring_target_id_foreign TO service_monitoring_target_department_service_monitoring_target_id_foreign');
        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_team_team_id_foreign TO service_monitoring_target_department_department_id_foreign');

        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_teams_pkey TO confidential_task_departments_pkey');
        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_teams_task_id_foreign TO confidential_task_departments_task_id_foreign');
        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_teams_team_id_foreign TO confidential_task_departments_department_id_foreign');

        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_managers_pkey TO service_request_type_manager_departments_pkey');
        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_managers_service_request_type_id_foreign TO service_request_type_manager_departments_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_managers_team_id_foreign TO service_request_type_manager_departments_department_id_foreign');

        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditors_pkey TO service_request_type_auditor_departments_pkey');
        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditors_service_request_type_id_foreign TO service_request_type_auditor_departments_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditors_team_id_foreign TO service_request_type_auditor_departments_department_id_foreign');
    }

    private function renameConstraintsBackward(): void
    {
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT departments_pkey TO teams_pkey');
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT departments_name_unique TO teams_name_unique');
        DB::statement('ALTER TABLE departments RENAME CONSTRAINT departments_division_id_foreign TO teams_division_id_foreign');

        DB::statement('ALTER TABLE users RENAME CONSTRAINT users_department_id_foreign TO users_team_id_foreign');

        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT advisories_assigned_department_id_foreign TO incidents_assigned_team_id_foreign');

        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_departments_pkey TO project_manager_teams_pkey');
        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_departments_project_id_foreign TO project_manager_teams_project_id_foreign');
        DB::statement('ALTER TABLE project_manager_departments RENAME CONSTRAINT project_manager_departments_department_id_foreign TO project_manager_teams_team_id_foreign');

        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_departments_pkey TO project_auditor_teams_pkey');
        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_departments_project_id_foreign TO project_auditor_teams_project_id_foreign');
        DB::statement('ALTER TABLE project_auditor_departments RENAME CONSTRAINT project_auditor_departments_department_id_foreign TO project_auditor_teams_team_id_foreign');

        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_department_pkey TO service_monitoring_target_team_pkey');
        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_department_service_monitoring_target_id_foreign TO service_monitoring_target_team_service_monitoring_target_id_foreign');
        DB::statement('ALTER TABLE service_monitoring_target_department RENAME CONSTRAINT service_monitoring_target_department_department_id_foreign TO service_monitoring_target_team_team_id_foreign');

        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_departments_pkey TO confidential_task_teams_pkey');
        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_departments_task_id_foreign TO confidential_task_teams_task_id_foreign');
        DB::statement('ALTER TABLE confidential_task_departments RENAME CONSTRAINT confidential_task_departments_department_id_foreign TO confidential_task_teams_team_id_foreign');

        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_manager_departments_pkey TO service_request_type_managers_pkey');
        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_manager_departments_service_request_type_id_foreign TO service_request_type_managers_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_manager_departments RENAME CONSTRAINT service_request_type_manager_departments_department_id_foreign TO service_request_type_managers_team_id_foreign');

        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditor_departments_pkey TO service_request_type_auditors_pkey');
        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditor_departments_service_request_type_id_foreign TO service_request_type_auditors_service_request_type_id_foreign');
        DB::statement('ALTER TABLE service_request_type_auditor_departments RENAME CONSTRAINT service_request_type_auditor_departments_department_id_foreign TO service_request_type_auditors_team_id_foreign');
    }

    private function renameAdvisoryConstraintsForward(): void
    {
        DB::statement('ALTER TABLE advisory_severities RENAME CONSTRAINT incident_severities_pkey TO advisory_severities_pkey');
        DB::statement('ALTER TABLE advisory_statuses RENAME CONSTRAINT incident_statuses_pkey TO advisory_statuses_pkey');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT incidents_pkey TO advisories_pkey');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT incidents_severity_id_foreign TO advisories_severity_id_foreign');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT incidents_status_id_foreign TO advisories_status_id_foreign');
        DB::statement('ALTER TABLE advisory_updates RENAME CONSTRAINT incident_updates_pkey TO advisory_updates_pkey');
        DB::statement('ALTER TABLE advisory_updates RENAME CONSTRAINT incident_updates_incident_id_foreign TO advisory_updates_advisory_id_foreign');
    }

    private function renameAdvisoryConstraintsBackward(): void
    {
        DB::statement('ALTER TABLE advisory_updates RENAME CONSTRAINT advisory_updates_advisory_id_foreign TO incident_updates_incident_id_foreign');
        DB::statement('ALTER TABLE advisory_updates RENAME CONSTRAINT advisory_updates_pkey TO incident_updates_pkey');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT advisories_status_id_foreign TO incidents_status_id_foreign');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT advisories_severity_id_foreign TO incidents_severity_id_foreign');
        DB::statement('ALTER TABLE advisories RENAME CONSTRAINT advisories_pkey TO incidents_pkey');
        DB::statement('ALTER TABLE advisory_statuses RENAME CONSTRAINT advisory_statuses_pkey TO incident_statuses_pkey');
        DB::statement('ALTER TABLE advisory_severities RENAME CONSTRAINT advisory_severities_pkey TO incident_severities_pkey');
    }
};
