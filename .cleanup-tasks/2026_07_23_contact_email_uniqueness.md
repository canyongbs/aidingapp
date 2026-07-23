---
title: Contact Email Uniqueness
created: 2026-07-23
---

## Feature Flags

None

## Temporary Migrations

Once `2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique` has run in
all environments, the one-time de-duplication logic can be removed from
`app-modules/contact/database/migrations/2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique.php`.
Keep the `citext` column conversion and the `contacts_email_unique` index — those are permanent.

- Remove the `$this->fixDuplicates()` call from `up()`
- Remove the `$this->revertDuplicates()` call from `down()`
- Remove the `$chunkSize` and `$usesSoftDeletes` properties
- Remove the `use FixesDuplicateNames;` trait
- Remove the email-specific override methods (`ignoresNullValues`, `orderDuplicateRecords`,
  `existingValueMatchPatterns`, `buildDeduplicatedValue`, `deduplicatedValuePattern`,
  `stripDeduplicatedSuffix`) and the now-unused `Str` import

## Additional Cleanup

- Remove the `2026_07_23_230730_convert_contacts_email_to_citext_and_enforce_unique`
  `describe(...)` block from `tests/TenantMigrationTests.php` (it only covers the temporary
  de-duplication behavior)
- If no other migration uses `FixesDuplicateNames`, restore its `// @phpstan-ignore trait.unused`
  annotation
