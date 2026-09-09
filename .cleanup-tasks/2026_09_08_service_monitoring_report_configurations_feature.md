---
title: Service Monitoring Report Configurations Feature
created: 2026-09-08
---

## Feature Flags

- App\Features\ServiceMonitoringReportConfigurationsFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_09_08_090400_tmp_backfill_service_monitoring_report_configurations.php

## Additional Cleanup

- Once every tenant has migrated, drop the legacy reporting columns (`is_reporting_active`, `report_frequency`, `is_reported_via_email`, `is_reported_via_database`) from `service_monitoring_targets`, and drop the legacy pivot tables `service_monitoring_target_report_user`, `service_monitoring_target_report_department`, `service_monitoring_target_report_contact` — along with the `ServiceMonitoringTarget` relations/casts/fillable entries and pivot models (`ServiceMonitoringTargetReportUser`, `ServiceMonitoringTargetReportDepartment`, `ServiceMonitoringTargetReportContact`) and their factories that reference them.
- Delete the tests that cover the inactive (pre-migration) legacy-target branch, since that branch no longer exists once the flag is removed:
    - `app-modules/service-management/tests/Tenant/Jobs/ServiceMonitoringReportJobTest.php` — `it('still dispatches for confidential targets, since delivery suppression happens per recipient')` should keep asserting against `ServiceMonitoringReportConfiguration`, but delete `it('falls back to legacy targets when the feature is inactive')` and `it('does not dispatch for legacy targets with inactive reporting when the feature is inactive')`.
    - Each also needs its `use App\Features\ServiceMonitoringReportConfigurationsFeature;` import removed if no longer referenced.
- Delete this file (`.cleanup-tasks/2026_09_08_service_monitoring_report_configurations_feature.md`) once all the above cleanup is complete.
