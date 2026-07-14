---
title: Managed Contact Feature
created: 2026-07-13
---

## Feature Flags

- `App\Features\ManagedContactFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/contact/database/migrations/2026_07_13_000000_add_user_id_to_contacts_table.php` (the `ManagedContactFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up. The schema change (`contacts.user_id`) is permanent and must be kept.

## Additional Cleanup

- Search for `TODO: Cleanup Task` in the codebase for inline cleanup instructions.

- In `app/Observers/UserObserver.php`: remove the `ManagedContactFeature::active()` guard in `saved()` / `deleted()` — the managed contact sync should always run.

- In `app/Filament/Resources/Users/Pages/CreateUser.php`, `EditUser.php`, and `ViewUser.php`: remove the `ManagedContactFeature::active()` conditions from the `->visible()` calls on the `is_managed_contact` toggle and `managed_contact_type_id` select — they should always be visible.

- In `app-modules/contact/src/Filament/Resources/ContactResource/Pages/ListContacts.php`: remove the `ManagedContactFeature::active()` guard around the lock icon on the name column.

- In `app-modules/contact/src/Filament/Resources/ContactResource/Pages/ViewContact.php` and `EditContact.php`: remove the `ManagedContactFeature::active()` guards that swap the edit action / disable the form for managed contacts — the lock behaviour should always apply.

- In `app/Providers/Filament/AdminPanelProvider.php`: remove the `ManagedContactFeature::active()` guard on the `Employee Self-Service` user menu item — it should always be visible (subject to the managed-contact check).

- In `app-modules/portal/routes/web.php`: the `employee-self-service` route guard does not depend on the flag, but confirm the controller no longer references `ManagedContactFeature::active()`.

- Delete the feature flag class: `app/Features/ManagedContactFeature.php`
