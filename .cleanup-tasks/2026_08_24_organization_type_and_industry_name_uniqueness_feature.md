---
title: Organization Type and Industry Name Uniqueness Feature
created: 2026-08-24
---

## Feature Flags

- App\Features\OrganizationTypeAndIndustryNameUniquenessFeature

## Temporary Migrations

## Additional Cleanup

- `app-modules/contact/database/migrations/2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique.php` — remove the `$this->fixDuplicates()` call (in `up()`), the `$this->revertDuplicates()` call (in `down()`), the `$chunkSize` and `$usesSoftDeletes` properties, and the `use FixesDuplicateNames;` trait. Do NOT remove the citext column conversions or the unique indexes — those are permanent.
- `tests/TenantMigrationTests.php` — delete the `describe('2026_08_24_000002_convert_organization_type_and_industry_name_to_citext_and_enforce_unique', ...)` block and remove any imports left unused (`OrganizationType`, `OrganizationIndustry`, `Command`, `Artisan`).
- If no other migration uses `FixesDuplicateNames` (`database/migrations/Concerns/FixesDuplicateNames.php`), restore its `// @phpstan-ignore trait.unused` annotation.
