---
title: Service Request Type Visibility Restrictions Cleanup
created: 2026-07-08
---

## Feature Flags

- App\Features\ServiceRequestTypeVisibilityRestrictionsFeature

## Temporary Migrations

## Additional Cleanup

Once `ServiceRequestTypeVisibilityRestrictionsFeature` is active in production and the migration
`app-modules/service-management/database/migrations/2026_07_07_154238_add_visibility_restriction_to_service_request_types.php`
has run across all environments, grep for `ServiceRequestTypeVisibilityRestrictionsFeature` and remove the
flag class, its migration `activate()`/`deactivate()` calls (the migration itself stays), the
controller/page guards (keep the active-path code, delete the unrestricted fallback branches), and
the blade flag check.

The following changes are **not** discoverable by searching for the flag name and must be reverted
by hand:

- `app-modules/service-management/resources/js/serviceRequestTypeManager.js` — remove the
  `visibilityRestrictionsEnabled` component property and the early return it drives in
  `renderVisibilityControl` so the control always renders.
- `app-modules/service-management/src/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypes.php`
  — drop the `bool $visibilityRestrictionsEnabled` parameter from `formatCategories()` and
  `formatTypes()`, and simplify `getHierarchicalData()` so `restrictedToContactTypes` is always
  eager loaded and the visibility fields are always included in the payload.
