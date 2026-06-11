---
title: Contact Type Management Feature
created: 2026-06-11
---

## Feature Flags

- App\Features\ContactTypeManagementFeature

## Temporary Migrations

## Additional Cleanup

Search the codebase for `TODO: ContactTypeManagementFeature cleanup` for inline cleanup instructions at each point of change.

Once the feature flag is removed, the application should behave as if the flag were always active (new `is_default` + `Color` enum behavior). Work through the following:

- In `app-modules/contact/src/Models/ContactType.php`:
    1. Remove the `'classification'` entry from `$fillable` and from `casts()`.
    2. Replace the dynamic color cast `ContactTypeManagementFeature::active() ? Color::class : ContactTypeColorOptions::class` with `Color::class`.
    3. Remove the `use AidingApp\Contact\Enums\SystemContactClassification;`, `use AidingApp\Contact\Enums\ContactTypeColorOptions;`, and `use App\Features\ContactTypeManagementFeature;` imports.

- Delete the now-unused enums:
    - `app-modules/contact/src/Enums/SystemContactClassification.php`
    - `app-modules/contact/src/Enums/ContactTypeColorOptions.php`

- Add a migration to drop the `classification` column from `contact_types` (it was only kept nullable for the flag-off fallback).

- In `app-modules/contact/database/migrations/2026_06_11_183351_add_is_default_to_contact_types_table.php` (the combined setup migration): remove the `ContactTypeManagementFeature::activate()` / `deactivate()` calls and the `use App\Features\ContactTypeManagementFeature;` import. Do NOT delete the whole migration — it also adds the permanent `is_default` column and performs the one-time color data conversion. Leaving the flag class referenced here would fatal on `migrate:fresh` once the class is deleted.

- In `app-modules/contact/src/Filament/Resources/ContactTypeResource/Pages/CreateContactType.php` and `EditContactType.php`: remove the old `classification` Select and the old `color` Select (the `->hidden(...)`/`->visible(...)` flag conditionals), keeping only the `ColorSelect` and the `is_default` Toggle unconditionally. Remove the feature flag and old enum imports.

- In `app-modules/contact/src/Filament/Resources/ContactTypeResource/Pages/ListContactTypes.php` and `ViewContactType.php`: remove the `classification` column/entry and its `->visible(...)` guard; show the `is_default` column/entry and `color` badge unconditionally. Remove the feature flag import.

- In `app-modules/contact/database/factories/ContactTypeFactory.php`: remove the `classification` definition and the feature flag conditional around `color`; always use `Color::cases()`. Remove the old enum and feature flag imports.

- In `app-modules/contact/database/seeders/ContactTypeSeeder.php`: no flag conditional should remain (already seeds the new shape) — verify and remove any leftover references.

- In `app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php` and `app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRegisterController.php`: remove the `ContactTypeManagementFeature::active()` ternary, keeping only the `ContactType::resolveDefault()` path. Remove the `use AidingApp\Contact\Enums\SystemContactClassification;` and `use App\Features\ContactTypeManagementFeature;` imports.

- In `app-modules/contact/src/Filament/Resources/ContactResource/Pages/CreateContact.php`: on the `type_id` Select, drop the `ContactTypeManagementFeature::active()` guard in `->default(...)` so it always defaults to `ContactType::resolveDefault()?->getKey()`. Remove the `use App\Features\ContactTypeManagementFeature;` import.

- In `app-modules/contact/src/Imports/ContactImporter.php`: in the `type` column's `resolveUsing` closure, drop the `ContactTypeManagementFeature::active()` guard so a blank/unmatched type always falls back to `ContactType::resolveDefault()`. Remove the `use App\Features\ContactTypeManagementFeature;` import.

- Update any tests that still exercise the flag-off (classification) path — notably `app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRegisterControllerTest.php`, which seeds a ContactType via `'classification' => SystemContactClassification::New` (replace with `'is_default' => true`). Remove the `use AidingApp\Contact\Enums\SystemContactClassification;` import there.

- Delete the feature flag class itself: `app/Features/ContactTypeManagementFeature.php`.
