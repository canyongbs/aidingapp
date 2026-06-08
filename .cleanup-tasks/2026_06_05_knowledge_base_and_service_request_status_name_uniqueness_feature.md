---
title: Knowledge Base And Service Request Status Name Uniqueness Feature
created: 2026-06-05
---

## Feature Flags

- App\Features\KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature

## Temporary Migrations

## Additional Cleanup

### Feature flag

- `database/migrations/2026_06_05_200024_activate_knowledge_base_and_service_request_status_name_uniqueness_feature.php` — this migration only activates the flag. When the flag class is deleted, delete this migration (or otherwise resolve its `KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature::activate()` reference) so a fresh migrate keeps working.
- `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatuses/Pages/CreateKnowledgeBaseStatus.php` — remove the `->when(KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature::active(), ...)` wrapper and apply `->unique(modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatuses/Pages/EditKnowledgeBaseStatus.php` — remove the `->when(...)` wrapper and apply `->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/service-management/src/Filament/Resources/ServiceRequestStatuses/Pages/CreateServiceRequestStatus.php` — remove the `->when(...)` wrapper and apply `->unique(modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/service-management/src/Filament/Resources/ServiceRequestStatuses/Pages/EditServiceRequestStatus.php` — remove the `->when(...)` wrapper and apply `->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed())` directly on the `name` field (inline `TODO` present).
- `app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseStatuses/Pages/CreateKnowledgeBaseStatusTest.php` — remove the `::activate()` calls and delete the "does not apply the unique form rule when the feature is disabled" test.
- `app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseStatuses/Pages/EditKnowledgeBaseStatusTest.php` — remove the `::activate()` calls.
- `app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/CreateServiceRequestStatusTest.php` — remove the `::activate()` calls and delete the "does not apply the unique form rule when the feature is disabled" test.
- `app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/EditServiceRequestStatusTest.php` — remove the `::activate()` calls.
- Delete `app/Features/KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature.php` and purge its stored values from the tenant `features` table (`App\Features\KnowledgeBaseAndServiceRequestStatusNameUniquenessFeature::purge()`).

### One-time data-fix migration scaffolding (independent of the flag, once run in all environments)

- `app-modules/knowledge-base/database/migrations/2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name.php` — remove the `mergeDuplicates()` call (in `up()`) and the `mergeDuplicates()` helper method. Do NOT remove the citext column conversion or the unique index — those are permanent.
- `app-modules/service-management/database/migrations/2026_06_05_193725_add_citext_unique_to_service_request_statuses_name.php` — remove the `DISABLE TRIGGER` / `ENABLE TRIGGER` statements, the `fixDuplicates()` call (in `up()`) and the `fixDuplicates()` / `mergeWithinClassifications()` / `renameAcrossClassifications()` / `generateUniqueName()` / `rewriteHistoryStatus()` helper methods. Do NOT remove the citext column conversion or the unique index — those are permanent.
- `tests/TenantMigrationTests.php` — when the scaffolding above is removed, delete the `describe('2026_06_04_203158_add_citext_unique_to_knowledge_base_statuses_name', ...)` and `describe('2026_06_05_193725_add_citext_unique_to_service_request_statuses_name', ...)` blocks and remove any imports left unused (`KnowledgeBaseItem`, `KnowledgeBaseStatus`, `Division`, `SystemServiceRequestClassification`, `ServiceRequest`, `ServiceRequestStatus`, `Str`).
- NOTE: the `ServiceRequestStatusSeeder` change (matching `firstOrCreate` on `name` + `classification` instead of `createOrFirst` on the full attribute set) is a permanent root-cause fix — keep it, it is not part of this cleanup.
