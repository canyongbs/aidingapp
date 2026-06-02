---
title: Knowledge Base Category Sort Feature
created: 2026-06-01
---

## Feature Flags

- App\Features\KnowledgeBaseCategorySortFeature

## Additional Cleanup

- In `app-modules/knowledge-base/database/migrations/2026_05_29_251423_add_sort_column_to_knowledge_base_categories_table.php`:
    1. Remove the data backfill blocks (parent categories + sub categories loops)
       -> Delete everything between the first `Schema::table()` and the second `Schema::table()`
    2. Remove the second `Schema::table()` call that changes sort to non-nullable
       -> Delete: `Schema::table('knowledge_base_categories', function (Blueprint $table) { $table->integer('sort')->default(0)->nullable(false)->change(); });`
    3. Change the first `Schema::table()` to add sort as non-nullable with `default(0)`
       -> Change to: `$table->integer('sort')->default(0);`
    4. Remove the `KnowledgeBaseCategorySortFeature::activate()` and `KnowledgeBaseCategorySortFeature::deactivate()` calls
    5. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import

- In `app-modules/knowledge-base/src/Observers/KnowledgeBaseCategoryObserver.php`: remove the `if (! KnowledgeBaseCategorySortFeature::active())` early return guard — the observer should always assign sort. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php`: in the `subCategories()` method, remove the `if (KnowledgeBaseCategorySortFeature::active())` conditional — always apply `->orderBy('sort')`. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategories/Pages/ListKnowledgeBaseCategories.php`: remove the `->when(KnowledgeBaseCategorySortFeature::active(), ...)` conditional — always apply `->defaultSort('sort')->reorderable('sort')` directly on the table. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategories/RelationManagers/SubCategoriesRelationManager.php`: remove the `->when(KnowledgeBaseCategorySortFeature::active(), ...)` conditional — always apply `->defaultSort('sort')->reorderable('sort')` directly on the table. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItems/Pages/CreateKnowledgeBaseItem.php`: remove the `KnowledgeBaseCategorySortFeature::active() ? 'sort' : 'name'` ternaries in `modifyQueryUsing` and `modifyChildQueryUsing` — keep only `$query->orderBy('sort')`. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItems/Pages/EditKnowledgeBaseItem.php`: remove the `KnowledgeBaseCategorySortFeature::active() ? 'sort' : 'name'` ternaries in `modifyQueryUsing` and `modifyChildQueryUsing` — keep only `$query->orderBy('sort')`. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- In `app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategoryController.php`: in `index()`, remove the ternary `KnowledgeBaseCategorySortFeature::active() ? 'sort' : 'name'` — keep only `->orderBy('sort')`. In `show()`, remove the `->when(KnowledgeBaseCategorySortFeature::active(), ...)` conditional — keep only `->orderBy('sort')`. Remove the `use App\Features\KnowledgeBaseCategorySortFeature;` import.

- Remove every remaining `use App\Features\KnowledgeBaseCategorySortFeature;` import that becomes unused after the above edits.

- Delete the feature flag class itself: `app/Features/KnowledgeBaseCategorySortFeature.php`.
