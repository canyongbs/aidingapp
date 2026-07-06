---
title: Pipeline Entry Fields Feature
created: 2026-07-06
---

## Feature Flags

- `App\Features\PipelineEntryFieldsFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/project/database/migrations/2026_07_03_121453_add_additional_fileds_to_pipeline_entries_table.php` (the `PipelineEntryFieldsFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up

## Additional Cleanup

- In `app-modules/project/src/Observers/PipelineEntryObserver.php`: remove the `PipelineEntryFieldsFeature::active()` guard in `saving()` — `created_by` auto-set should always run. Remove the `PipelineEntryFieldsFeature::active()` guard in `saved()` — notification dispatch should always run.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/ManagePipelineEntries.php`: remove the `PipelineEntryFieldsFeature::active()` conditions from the six `->visible()` and `->required()` calls on the new form fields (`description`, `due`, `assigned_to_type`, `assigned_to`, `related_to_type`, `related_to`) — they should always be visible. Simplify `->visible()` and `->required()` on `TableSelect` fields to just the `$get()` condition.

- In `app-modules/project/src/Livewire/PipelineEntryKanban.php`: same as above for the six form fields. Also remove the `PipelineEntryFieldsFeature::active()` guard in the `->action()` callback — `description`, `due`, `assigned_to`, `related_to` should always be persisted.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/EditPipelineEntry.php`: remove the `PipelineEntryFieldsFeature::active()` conditions from the six `->visible()` and `->required()` calls. Remove the `PipelineEntryFieldsFeature::active() ? 'Name' : 'Description'` ternary on `TextInput::make('name')` — label should always be `'Name'`. Remove the `PipelineEntryFieldsFeature::active()` guard in `save()` — toggle-based null-out of `assigned_to`/`related_to` should always run. Remove the `PipelineEntryFieldsFeature::active()` guard in `fillForm()` — toggle state hydration should always run.

- In `app-modules/project/src/Filament/Resources/Pipelines/Pages/ViewPipelineEntry.php`: remove the `PipelineEntryFieldsFeature::active()` conditions from the four `->visible()` calls on the new infolist entries (`description`, `due`, `assignedTo.name`, related contact) — they should always be visible.

- Delete the feature flag class: `app/Features/PipelineEntryFieldsFeature.php`

