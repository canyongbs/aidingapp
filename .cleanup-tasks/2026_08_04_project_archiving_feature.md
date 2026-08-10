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
    - In `getEloquentQuery()`, replace `->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived())` with a direct `->withoutArchived()`.
    - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Filament/Resources/Projects/Pages/ListProjects.php`
    - Remove `->visible(fn (): bool => ProjectArchivingFeature::active())` from the row `ArchiveAction::make()`.
    - In `toolbarActions()`, replace the `ProjectArchivingFeature::active() ? ArchiveBulkAction::make()... : DeleteBulkAction::make()...` ternary with just the `ArchiveBulkAction::make()->authorizeIndividualRecords('delete')` branch.
    - Remove the now-unused `use Filament\Actions\DeleteBulkAction;` import.
    - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Filament/Resources/Projects/Pages/EditProject.php`
    - In `getHeaderActions()`, replace `ProjectArchivingFeature::active() ? ArchiveAction::make() : DeleteAction::make()` with just `ArchiveAction::make()`.
    - Remove the now-unused `use Filament\Actions\DeleteAction;` import.
    - Remove the `use App\Features\ProjectArchivingFeature;` import.
- `app-modules/project/src/Policies/PipelinePolicy.php`
    - In `view()` and `update()`, change `if (ProjectArchivingFeature::active() && $pipeline->project?->isArchived())` to `if ($pipeline->project?->isArchived())`.
    - Remove the `use App\Features\ProjectArchivingFeature;` import.

- `app-modules/project/database/migrations/2026_08_04_093019_add_archived_at_to_projects_table.php` — this migration is permanent; keep the schema changes but remove the feature-flag activation inside it: delete the `ProjectArchivingFeature::activate()` call in `up()`, the `ProjectArchivingFeature::deactivate()` call in `down()`, and the `use App\Features\ProjectArchivingFeature;` import. If those were the only statements left in the `DB::transaction()` closures, simplify back to a plain `Schema::table(...)` call.

- Delete the tests that cover the inactive (pre-migration) branch, since that branch no longer exists. Each also needs its `use App\Features\ProjectArchivingFeature;` import removed.
    - `app-modules/project/tests/Tenant/Filament/Resources/ProjectResource/Pages/ListProjectsTest.php` — `it('does not show the archive actions when `ProjectArchivingFeature` is inactive')`.
    - `app-modules/project/tests/Tenant/Filament/Resources/ProjectResource/Pages/EditProjectTest.php` — `it('offers the `DeleteAction` instead of the `ArchiveAction` when `ProjectArchivingFeature` is inactive')`.
    - `app-modules/project/tests/Tenant/Policies/PipelinePolicyTest.php` — `it('still allows viewing a pipeline whose project is archived when `ProjectArchivingFeature` is inactive')`.

- Delete this file (`.cleanup-tasks/2026_08_04_project_archiving_feature.md`) once all the above cleanup is complete.
