---
title: Contact Email Uniqueness Feature
created: 2026-07-23
---

## Feature Flags

- App\Features\ContactEmailUniquenessFeature

## Temporary Migrations

## Additional Cleanup

- `app-modules/contact/database/migrations/2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique.php` — remove the `ContactEmailUniquenessFeature::activate()` call (in `up()`) and the `ContactEmailUniquenessFeature::deactivate()` call (in `down()`), the `$this->fixDuplicates()` call (in `up()`), the `$this->revertDuplicates()` call (in `down()`), the `$chunkSize` and `$usesSoftDeletes` properties, the `use FixesDuplicateNames;` trait, and the email-specific override methods (`ignoresNullValues`, `orderDuplicateRecords`, `existingValueMatchPatterns`, `buildDeduplicatedValue`, `deduplicatedValuePattern`, `stripDeduplicatedSuffix`) plus the now-unused `Str` import. Do NOT remove the citext column conversion or the unique index — those are permanent.
- `tests/TenantMigrationTests.php` — delete the `describe('2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique', ...)` block and remove any imports left unused (`Contact`, `User`, `Command`, `Artisan`).
- If no other migration uses `FixesDuplicateNames` (`database/migrations/Concerns/FixesDuplicateNames.php`), restore its `// @phpstan-ignore trait.unused` annotation.
- `app-modules/contact/src/Filament/Resources/ContactResource/Pages/CreateContact.php` — remove the `->when(ContactEmailUniquenessFeature::active(), ...)` wrapper and apply `->unique(modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `email` field (inline `TODO` present).
- `app-modules/contact/src/Filament/Resources/ContactResource/Pages/EditContact.php` — remove the `->when(...)` wrapper and apply `->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `email` field (inline `TODO` present).
- `app-modules/contact/tests/Tenant/Contact/CreateContactTest.php` — remove the `::activate()` call and delete the "does not apply the unique form rule when the feature is disabled" test.
- `app-modules/contact/tests/Tenant/Contact/EditContactTest.php` — remove the `::activate()` call.
- Delete `app/Features/ContactEmailUniquenessFeature.php`
