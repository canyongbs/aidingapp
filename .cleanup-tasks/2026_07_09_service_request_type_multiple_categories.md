---
title: Service Request Type Multiple Categories Cleanup
created: 2026-07-09
---

## Feature Flags

- App\Features\ServiceRequestTypeMultipleCategoriesFeature

## Temporary Migrations

- None. The backfill and flag activation live inside the permanent migration
  `2026_07_09_123757_migrate_service_request_types_to_multiple_categories` (see Additional Cleanup).

## Additional Cleanup

Once `ServiceRequestTypeMultipleCategoriesFeature` is active in production and the migration
`2026_07_09_123757_migrate_service_request_types_to_multiple_categories` has run across all
environments, grep for `ServiceRequestTypeMultipleCategoriesFeature` and remove the flag class and
every guard, keeping the active (pivot-based) path and deleting the legacy `category_id` fallback
branches.

The migration `2026_07_09_123757_migrate_service_request_types_to_multiple_categories` is
**permanent** and must **not** be deleted — it defines the `service_request_category_types` pivot
schema, which fresh installs still need.

The legacy `category_id` column on `service_request_types` is deliberately **kept** by this migration
as a rollback safety net; it is not dropped while the feature is bedding in. Once
`ServiceRequestTypeMultipleCategoriesFeature` is proven in production, drop it with a **new** cleanup
migration (editing this migration to add the drop would only affect fresh installs, not environments
that already ran it):

- `Schema::table('service_request_types', fn (Blueprint $table) => $table->dropConstrainedForeignId('category_id'));`

During cleanup, also strip everything in this migration that only exists to bridge the transition,
leaving a plain schema migration:

- In `up()`: remove the `insertUsing(...)` backfill and the
  `ServiceRequestTypeMultipleCategoriesFeature::activate()` call. Keep the
  `Schema::create('service_request_category_types', ...)`.
- In `down()`: remove the `ServiceRequestTypeMultipleCategoriesFeature::deactivate()` call and the
  `DB::statement(...)` that restores `category_id` from the pivot. Keep the pivot `dropIfExists`.

The backfill is one-time data code: once the migration has run across every existing environment it
only ever runs against an empty `service_request_types` table on fresh installs, so it is safe to
remove.

The following are **not** discoverable by searching for the flag name:

- Delete the `category()` `belongsTo` relationship on `ServiceRequestType` (only referenced by the
  removed legacy path).
- Remove `'category_id'` from the `$fillable` array on `ServiceRequestType` (the column no longer
  exists once the migration has run).
- In the `list-service-request-types` Blade view the flag is passed to the Alpine component as the
  `multipleCategoriesEnabled` prop. In `serviceRequestTypeManager.js` that prop gates the multi-area
  UI: the "Add existing type" button, the existing-type `<select>` row, and the `canRemove`
  ("Remove from this area") button state. On cleanup, drop the prop and the `this.multipleCategoriesEnabled`
  guards so the multi-area behaviour is always on. (The JS itself never names the flag class.)
- In `ViewServiceRequestType` the `service_request_areas` entry has a flag guard: keep the
  `$record->categories` branch and delete the legacy `$record->category` branch.

Everything else is a flag guard: grepping `ServiceRequestTypeMultipleCategoriesFeature` finds the
branches in `ServiceRequestType::categoryIds()`/`categorySortMap()`/`visibilityRestrictionParents()`,
the page's `syncTypeCategory()`/`assignTypeToCategory()`/`applyPendingTypePlacements()`/`resolveSaveTypeId()`/
`resolveTypeSort()`/`deleteCategoryWithDescendants()`, the observer's `creating()`, the inlined
`->when(..., fn ($query) => $query->with('categories:id'))` eager loads (in `ListServiceRequestTypes`,
`ListServiceRequests`, and `BuildContactServiceRequestTypeTree`), and the uncategorized-types query.
In each, delete the legacy `category_id` branch and keep the `categories()` pivot branch.

Tests attach a type to an area with the native `ServiceRequestType::factory()->hasAttached($category,
relationship: 'categories')` helper, which already assumes the pivot; no flag branch there to remove.
