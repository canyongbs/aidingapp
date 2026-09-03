---
title: Project Milestone Status Removed
created: 2026-09-02
---

## Feature Flags

- App\Features\ProjectMilestoneStatusRemovedFeature

## Additional Cleanup

- Search for `TODO: Cleanup Task (project-milestone-status-removed)` and follow the instructions at each site.
- Delete `AidingApp\Project\Models\ProjectMilestoneStatus`, `ProjectMilestoneStatusPolicy`, `ProjectMilestoneStatusFactory`, `ProjectMilestoneStatusSeeder`, and the `ProjectMilestoneStatuses` Filament resource.
- Remove `status_id` from `ProjectMilestone::$fillable`, delete its `status()` relation, the `project_milestone_status` morph map entry in `ProjectServiceProvider`, and the `ProjectMilestoneStatus::class` entry in `tests/Tenant/Unit/legacy-v4-uuid-models.php`.
- The `status_id` column and `project_milestone_statuses` table were already dropped by the flag's own migration; no further schema change is needed.
