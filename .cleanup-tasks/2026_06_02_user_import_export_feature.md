---
title: User Import Export Feature
created: 2026-06-02
---

## Feature Flags

- App\Features\UserImportExportFeature

## Temporary Migrations

## Additional Cleanup

- `app/Filament/Imports/UserImporter.php` — drop the `...(UserImportExportFeature::active() ? [...] : [])` spread in `getColumns()` so the enhanced columns (Work Number, Work Extension, Mobile number, Department, Assigned Role) are always present.
- `app/Filament/Resources/Users/Pages/ListUsers.php` — remove `->visible(fn () => UserImportExportFeature::active())` from the `ExportAction` (leave the `->authorize('import', User::class)` check).
- `tests/Tenant/Feature/Filament/Imports/UserImporterTest.php` — remove the `beforeEach(fn () => UserImportExportFeature::activate())` and the "only exposes the enhanced import columns when the feature is active" test.
- `app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php` — drop the `UserImportExportFeature::activate()` call.
- Delete `app/Features/UserImportExportFeature.php` and purge its stored values from the tenant `features` table (`App\Features\UserImportExportFeature::purge()`).
- The name/email citext migrations stay, but their one-time duplicate-renaming scaffolding can be stripped once they have run in all environments (each carries an inline `TODO`):
- `app-modules/department/database/migrations/2026_06_02_143001_add_citext_unique_to_departments_name.php`
- `app-modules/authorization/database/migrations/2026_06_02_143002_add_citext_unique_to_roles_name.php`
- `database/migrations/2026_06_02_143003_add_citext_unique_to_users_email.php`
For each: remove the `fixDuplicates()` call (in `up()`) and the `revertDuplicates()` call (in `down()`), plus the `$chunkSize` / `$usesSoftDeletes` / `$groupByColumns` / `$ignoreNullValues` helper properties. Do NOT remove the citext column conversion, the unique index, or the `user.import` permission — those are permanent.
