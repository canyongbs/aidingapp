---
title: Project New Fields Feature
created: 2026-07-13
---

## Feature Flags

- `App\Features\ProjectNewFieldsFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/project/database/migrations/2026_07_13_165911_data_activate_project_new_fields_feature.php` (the `ProjectNewFieldsFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up

## Additional Cleanup

- In `app-modules/project/src/Filament/Resources/Projects/Forms/ProjectForm.php`: remove the `ProjectNewFieldsFeature::active()` guards from the `->required()` and `->visible()` calls on `icon` and `color` fields — they should always be required and visible. Remove the `ProjectNewFieldsFeature::active()` guard from the `->visible()` calls on `department_id`, `start_date`, `target_completion_date_type`, and `target_completion_date` fields — they should always be visible. Simplify `target_completion_date` visibility to just the `$get('target_completion_date_type') === 'set'` condition.

- In `app-modules/project/src/Filament/Resources/Projects/Pages/ViewProject.php`: remove the `ProjectNewFieldsFeature::active()` guards from the `->visible()` calls on the five new infolist entries (`icon`, `color`, `department.name`, `start_date`, `target_completion_date`) — they should always be visible.

- In `app-modules/project/src/Filament/Resources/Projects/Pages/ManageGuests.php`: remove the `ProjectNewFieldsFeature::active()` guard from `canAccess()` — the Guests page should always be accessible (keep the permission check).

- Delete the feature flag class: `app/Features/ProjectNewFieldsFeature.php`
