---
title: Change Request Permission Compression
created: 2026-11-06
---

## Feature Flags

- App\Features\ChangeRequestPermissionCompressionFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_11_06_233425_tmp_grant_change_request_permissions_to_users_and_roles_with_related_permissions.php

## Additional Cleanup

- Search for `TODO: Cleanup Task - Compress Change Request Permissions` and follow the instructions at each site.

<!--
Only list cleanup that has no home in code. Do NOT restate obvious feature-flag
removals (keep the active path, drop the inactive path), and do NOT include file
paths or line numbers. For non-obvious changes, put a `TODO: Cleanup Task (<tag>)`
comment at the change site and reference the tag here — e.g.
"Search for `TODO: Cleanup Task (some-feature)`".
-->
