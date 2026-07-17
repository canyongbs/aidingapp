---
title: Pipeline Stage Classification Feature
created: 2026-07-14
---

## Feature Flags

- `App\Features\PipelineStageClassificationFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/project/database/migrations/2026_07_14_000001_add_classification_to_pipeline_stages_table.php` (the `PipelineStageClassificationFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up

## Additional Cleanup

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/CreatePipeline.php`: remove the `PipelineStageClassificationFeature::active()` condition from the classification `Select` field's `->visible()` call — it should always be visible. Remove the ternary in `->default()` on the Repeater — the default stages should always include `classification`.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/EditPipeline.php`: remove the `PipelineStageClassificationFeature::active()` condition from the classification `Select` field's `->visible()` call — it should always be visible.

- In `app-modules/project/src/Filament/Resources/Projects/Pages/ManagePipelines.php`: remove the `PipelineStageClassificationFeature::active()` condition from the classification `Select` field's `->visible()` call — it should always be visible.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/ViewPipeline.php`: remove the `PipelineStageClassificationFeature::active()` condition from the classification `TextEntry`'s `->visible()` call — it should always be visible.

- Delete the feature flag class: `app/Features/PipelineStageClassificationFeature.php`
