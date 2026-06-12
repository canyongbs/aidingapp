---
title: Data Migrate Service Request Type Email Preferences
created: 2026-06-02
---

## Feature Flags

- App\Features\ServiceRequestTypeEmailPreferenceFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_06_02_122811_tmp_data_migrate_service_request_type_email_preferences.php

## Additional Cleanup

- Create a new migration that drops all of the old boolean preference columns from the `service_request_types` table (e.g. `is_managers_service_request_created_email_enabled`, `is_auditors_service_request_assigned_notification_enabled`, etc.) and remove any references to them in models, factories, or other code.
