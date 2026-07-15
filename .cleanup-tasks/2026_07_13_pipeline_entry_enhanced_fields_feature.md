---
title: Pipeline Entry Enhanced Fields Feature
created: 2026-07-13
---

## Feature Flags

- `App\Features\PipelineEntryEnhancedFieldsFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/project/database/migrations/2026_07_13_512015_enhance_pipeline_entries_with_new_capabilities.php` (the `PipelineEntryEnhancedFieldsFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up

## Additional Cleanup

- In `app-modules/project/src/Observers/PipelineEntryObserver.php`: remove the `PipelineEntryEnhancedFieldsFeature::active()` guard in `saved()` — notification dispatch should always run when assigned to a user.

- In `app-modules/project/src/Filament/Resources/Pipelines/Forms/PipelineEntryForm.php`: remove the `PipelineEntryEnhancedFieldsFeature::active()` conditions from the five `->visible()` calls on the enhanced form fields (`assignedTo`, `is_visible_to_guests`, `milestones`, `assets`, `serviceRequests`) — they should always be visible.

- In `app-modules/project/src/Livewire/PipelineEntryKanban.php`: remove the two `PipelineEntryEnhancedFieldsFeature::active()` guards in the `->action()` callback — `assigned_to_type`, `assigned_to_id`, `is_visible_to_guests` should always be persisted, and milestone/asset/service request syncing should always run.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/EditPipelineEntry.php`: remove the `PipelineEntryEnhancedFieldsFeature::active()` guard in `save()` — milestone/asset/service request syncing should always run. Remove the `PipelineEntryEnhancedFieldsFeature::active()` guard in `fillForm()` — M2M relationship hydration should always run.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/ViewPipelineEntry.php`: remove the `PipelineEntryEnhancedFieldsFeature::active()` conditions from the six `->visible()` calls on the enhanced infolist entries (`assignedTo`, `assigned_to_type`, `is_visible_to_guests`, `milestones.title`, `assets.name`, `serviceRequests.title`) — they should always be visible.

- Delete the feature flag class: `app/Features/PipelineEntryEnhancedFieldsFeature.php`
