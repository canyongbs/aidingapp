---
title: Project Milestone Status Removed
created: 2026-09-02
---

## Feature Flags

- App\Features\ProjectMilestoneStatusRemovedFeature

## Additional Cleanup

- Search for `TODO: Cleanup Task (project-milestone-status-removed)` and follow the instructions at each site.
- Delete `AidingApp\Project\Models\ProjectMilestoneStatus`, `ProjectMilestoneStatusFactory`, and `ProjectMilestoneStatusSeeder`.
- Remove `status_id` from `ProjectMilestone::$fillable`, delete its `status()` relation, the `project_milestone_status` morph map entry in `ProjectServiceProvider`, and the `ProjectMilestoneStatus::class` entry in `tests/Tenant/Unit/legacy-v4-uuid-models.php`.
- Delete `app-modules/project/tests/Tenant/Filament/Actions/CreateProjectMilestoneActionTest.php` — both tests only cover the status field being flag-gated, which won't exist anymore.
