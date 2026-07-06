---
title: Pipeline Entry Fields Feature
created: 2026-07-06
---

## Feature Flags

- `App\Features\PipelineEntryFieldsFeature`
    - Defined in `app/Features/PipelineEntryFieldsFeature.php`
    - Default: `false` (inactive)
    - Activated by migration `2026_07_03_121453_add_additional_fileds_to_pipeline_entries_table` via `PipelineEntryFieldsFeature::activate()` on `up()`, deactivated on `down()`

### Files using the flag

| File                                                                                                            | Usage                                                                                                                               |
| --------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| `app-modules/project/src/Observers/PipelineEntryObserver.php`                                                   | Gates `created_by` auto-set in `saving()` and notification dispatch in `saved()`                                                    |
| `app-modules/project/src/Filament/Resources/Pipelines/Pages/ManagePipelineEntries.php`                          | Gates visibility/required on 6 new form fields in the create modal                                                                  |
| `app-modules/project/src/Livewire/PipelineEntryKanban.php`                                                      | Gates visibility/required on 6 new form fields and new field persistence in `->action()` callback                                   |
| `app-modules/project/src/Filament/Resources/Pipelines/Pages/EditPipelineEntry.php`                              | Gates visibility/required on 6 new form fields, label swap on `name`, toggle null-out in `save()`, toggle hydration in `fillForm()` |
| `app-modules/project/src/Filament/Resources/Pipelines/Pages/ViewPipelineEntry.php`                              | Gates visibility on 4 new infolist entries                                                                                          |
| `app-modules/project/database/migrations/2026_07_03_121453_add_additional_fileds_to_pipeline_entries_table.php` | Activates/deactivates the flag                                                                                                      |

### Gated fields

These fields are hidden when the flag is inactive:

- `Textarea::make('description')`
- `DateTimePicker::make('due')`
- `ToggleButtons::make('assigned_to_type')` + `TableSelect::make('assigned_to')`
- `ToggleButtons::make('related_to_type')` + `TableSelect::make('related_to')`

### Gated behaviour

- **Observer `saving()`** — `created_by` auto-set from `auth()->id()` only runs when flag is active
- **Observer `saved()`** — `PipelineEntryAssignedToUserNotification` only dispatched when flag is active
- **Kanban `->action()` callback** — `description`, `due`, `assigned_to`, `related_to` only persisted when flag is active
- **Edit `save()`** — toggle-based null-out of `assigned_to`/`related_to` only runs when flag is active
- **Edit `fillForm()`** — `assigned_to_type`/`related_to_type` toggle state only hydrated when flag is active
- **Edit `name` label** — shows `'Name'` when active, `'Description'` when inactive

## Temporary Migrations

## Additional Cleanup
