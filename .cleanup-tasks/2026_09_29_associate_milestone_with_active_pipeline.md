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
    - The permanent `add_pipeline_id_to_project_milestones` migration keeps referencing the flag class and must be edited, since it cannot be deleted.
