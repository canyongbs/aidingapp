---
title: Enhance Contacts Table Data Model
created: 2026-09-24
---

## Feature Flags

- App\Features\EnhanceContactsTableDataModelFeature

## Temporary Migrations

## Additional Cleanup

- Search for `TODO: Cleanup Task (enhance-contacts-data-model)` and follow the instructions at each site. This covers the gated form fields (`ContactFormSchema`), import columns (`ContactImporter`), and the flag activation/deactivation embedded in the permanent `2026_09_23_251436_enhance_contacts_table_data_model` migration (remove the `activate()`/`deactivate()` calls, the surrounding `DB::transaction`, and the feature-flag import — keep the schema changes).
