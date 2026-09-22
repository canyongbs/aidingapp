---
title: Service Request Status Archiving Feature
created: 2026-09-22
---

## Feature Flags

- App\Features\ServiceRequestStatusArchivingFeature

## Temporary Migrations

## Additional Cleanup

- The migration that adds the `archived_at` column to `service_request_statuses` is permanent. Keep the schema change and remove only the feature-flag activation and deactivation from it.

<!--
Only list cleanup that has no home in code. Do NOT restate obvious feature-flag
removals (keep the active path, drop the inactive path), and do NOT include file
paths or line numbers. For non-obvious changes, put a `TODO: Cleanup Task (<tag>)`
comment at the change site and reference the tag here — e.g.
"Search for `TODO: Cleanup Task (some-feature)`".
-->
