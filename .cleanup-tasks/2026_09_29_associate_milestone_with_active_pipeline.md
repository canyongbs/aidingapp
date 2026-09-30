---
title: Associate Milestone With Active Pipeline
created: 2026-09-29
---

## Feature Flags

- App\Features\AssociateMilestoneWithActivePipelineFeature

## Temporary Migrations

## Additional Cleanup

- Search for `TODO: Cleanup Task (associate-milestone-with-active-pipeline)` and follow the instructions at each site:
    - `CreateProjectMilestoneAction` gates setting `pipeline_id` behind the flag — once removed, always set it and drop the feature import.
    - `ProjectWorkPipelineWidget` gates scoping empty-milestone placeholder rows to the selected pipeline — once removed, always apply the `pipeline_id` filter and drop the feature import.
    - `PipelineEntryForm` gates scoping the milestone select to the entry's pipeline — once removed, always scope by `pipeline_id` and drop the `project_id` branch and feature import.
    - `Pipeline::booted()` gates archiving a pipeline's milestones when the pipeline is archived — once removed, always archive them and drop the feature import.
    - The permanent `add_pipeline_id_to_project_milestones` migration keeps referencing the flag class and must be edited, since it cannot be deleted.
