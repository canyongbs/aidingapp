---
title: Knowledge Base Status Name Uniqueness Feature
created: 2026-06-04
---

## Feature Flags

- App\Features\KnowledgeBaseStatusNameUniquenessFeature

## Temporary Migrations

## Additional Cleanup

- `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatuses/Pages/CreateKnowledgeBaseStatus.php` — remove the `->when(KnowledgeBaseStatusNameUniquenessFeature::active(), ...)` wrapper and apply `->unique(modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatuses/Pages/EditKnowledgeBaseStatus.php` — remove the `->when(KnowledgeBaseStatusNameUniquenessFeature::active(), ...)` wrapper and apply `->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseStatuses/Pages/CreateKnowledgeBaseStatusTest.php` — remove the `KnowledgeBaseStatusNameUniquenessFeature::activate()` calls and delete the "does not apply the unique form rule when the feature is disabled" test.
- `app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseStatuses/Pages/EditKnowledgeBaseStatusTest.php` — remove the `KnowledgeBaseStatusNameUniquenessFeature::activate()` calls.
- Delete `app/Features/KnowledgeBaseStatusNameUniquenessFeature.php` and purge its stored values from the tenant `features` table (`App\Features\KnowledgeBaseStatusNameUniquenessFeature::purge()`).
- `app-modules/knowledge-base/database/migrations/2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name.php` — once it has run in all environments, remove the one-time `mergeDuplicates()` call (in `up()`) and the `mergeDuplicates()` helper method. Do NOT remove the citext column conversion or the unique index — those are permanent. The migration also calls `KnowledgeBaseStatusNameUniquenessFeature::activate()`; when the flag class is deleted, inline that activation (or otherwise resolve the reference) so the migration keeps running on a fresh database.
- `tests/TenantMigrationTests.php` — when the `mergeDuplicates()` scaffolding is removed above, delete the `describe('2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name', ...)` block (which asserts the duplicate merge / article reassignment) and remove the now-unused `KnowledgeBaseItem` and `KnowledgeBaseStatus` imports if no other test references them.
