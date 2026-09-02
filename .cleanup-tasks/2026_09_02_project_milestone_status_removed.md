---
title: Project Milestone Status Removed
created: 2026-09-02
---

## Feature Flags

- App\Features\ProjectMilestoneStatusRemovedFeature

## Additional Cleanup

- Search for `TODO: Cleanup Task (project-milestone-status-removed)` and follow the instructions at each site.
- Delete `AidingApp\Project\Models\ProjectMilestoneStatus`, `ProjectMilestoneStatusPolicy`, `ProjectMilestoneStatusFactory`, `ProjectMilestoneStatusSeeder`, and the `ProjectMilestoneStatuses` Filament resource (and its test).
- Remove `status_id` from `ProjectMilestone::$fillable`, delete its `status()` relation, the `project_milestone_status` morph map entry in `ProjectServiceProvider`, the `ProjectMilestoneStatusSeeder` registration in `NewTenantSeeder`, and the `ProjectMilestoneStatus::class` entry in `tests/Tenant/Unit/legacy-v4-uuid-models.php`.
- Add a migration that drops the now-unused `status_id` column (`dropConstrainedForeignId('status_id')`) from `project_milestones` and drops the `project_milestone_statuses` table.
