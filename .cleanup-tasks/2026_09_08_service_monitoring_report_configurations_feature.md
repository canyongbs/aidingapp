---
title: Service Monitoring Report Configurations Feature
created: 2026-09-08
---

## Feature Flags

- App\Features\ServiceMonitoringReportConfigurationsFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_09_08_090400_tmp_backfill_service_monitoring_report_configurations.php

## Additional Cleanup

- Once every tenant has migrated, remove the legacy reporting columns, pivot tables, and model compatibility that are no longer needed.
- Remove all inactive pre-migration compatibility tests after the legacy branch is removed.
- Delete this file (`.cleanup-tasks/2026_09_08_service_monitoring_report_configurations_feature.md`) once all the above cleanup is complete.
