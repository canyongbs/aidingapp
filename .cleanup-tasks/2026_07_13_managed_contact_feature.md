---
title: Managed Contact Feature
created: 2026-07-13
---

## Feature Flags

- `App\Features\ManagedContactFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/contact/database/migrations/2026_07_13_074512_add_user_id_to_contacts_table.php` (the `ManagedContactFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up. The schema change (`contacts.user_id`) is permanent and must be kept.

## Additional Cleanup

None. Removing this flag is the standard removal of every `ManagedContactFeature::active()` check (keeping the active-path behaviour) plus removing the activation call noted above. All managed-contact functionality — the `UserObserver`, `ManagedContactService`, the user form fields, the `ContactResource` lock UI, and the `employee-self-service` route/menu item — is permanent and stays.

