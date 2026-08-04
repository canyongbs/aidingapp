---
title: Project Archiving Feature Flag
created: 2026-08-04
---

## Feature Flags

- `App\Features\ProjectArchivingFeature`

## Temporary Migrations


## Additional Cleanup

- `app/Features/ProjectArchivingFeature.php` — delete the whole file.

- `app-modules/project/src/Filament/Resources/Projects/ProjectResource.php`
  - In `getEloquentQuery()` and `getGlobalSearchEloquentQuery()`, replace `->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived())` with a direct `->withoutArchived()`.
  - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Filament/Resources/Projects/Pages/ListProjects.php`
  - In `modifyQueryUsing()`, replace `->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived())` with a direct `->withoutArchived()`.
  - Remove `->visible(fn (): bool => ProjectArchivingFeature::active())` from the row `Action::make('archive')` and from `ArchiveBulkAction::make()`.
  - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Filament/Resources/Projects/Pages/EditProject.php`
  - Remove `->visible(fn (): bool => ProjectArchivingFeature::active())` from `ArchiveAction::make()`.
  - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Policies/PipelinePolicy.php`
  - In `view()` and `update()`, change `if (ProjectArchivingFeature::active() && $pipeline->project?->isArchived())` to `if ($pipeline->project?->isArchived())`.
  - Remove the `use App\Features\ProjectArchivingFeature;` import.

- `app-modules/project/database/migrations/2026_08_04_093019_add_archived_at_to_projects_table.php` — this migration is permanent; keep the schema changes but remove the feature-flag activation inside it: delete the `ProjectArchivingFeature::activate()` call in `up()`, the `ProjectArchivingFeature::deactivate()` call in `down()`, and the `use App\Features\ProjectArchivingFeature;` import. If those were the only statements left in the `DB::transaction()` closures, simplify back to a plain `Schema::table(...)` call.

- Delete this file (`.cleanup-tasks/2026_08_04_project_archiving_feature.md`) once all the above cleanup is complete.
