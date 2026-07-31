---
title: Convert Literal Merge Tags
created: 2026-07-30
---

## Feature Flags

## Temporary Migrations

- `app-modules/engagement/database/migrations/2026_07_30_182335_tmp_data_convert_literal_merge_tags_in_engagement_rich_content.php`
- `app-modules/service-management/database/migrations/2026_07_30_182417_tmp_data_convert_literal_merge_tags_in_service_request_email_templates.php`

## Additional Cleanup

- `tests/TenantMigrationTests.php` — delete the `describe('2026_07_30_182335_tmp_data_convert_literal_merge_tags_in_engagement_rich_content', ...)` and `describe('2026_07_30_182417_tmp_data_convert_literal_merge_tags_in_service_request_email_templates', ...)` blocks, and remove any imports left unused.

Note: the `saving` observers that call `CanyonGBS\Common\Support\ConvertLiteralMergeTags` are permanent and must NOT be removed — only the one-time backfill migrations above are temporary.
