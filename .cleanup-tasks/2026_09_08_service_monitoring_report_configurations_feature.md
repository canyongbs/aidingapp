---
title: Service Monitoring Report Configurations Feature
created: 2026-09-08
---

## Feature Flags

- App\Features\ServiceMonitoringReportConfigurationsFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_09_08_090400_tmp_backfill_service_monitoring_report_configurations.php

## Additional Cleanup

- Remove the legacy reporting columns from `service_monitoring_targets` (`report_frequency`, `is_reporting_active`, `is_reported_via_email`, `is_reported_via_database`), added by `app-modules/service-management/database/migrations/2026_08_05_124954_add_is_reporting_active_report_frequency_and_report_channel_to_service_monitoring_targets_table.php`.

- Drop the legacy per-target report recipient pivot tables and their models:
    - `service_monitoring_target_report_user` (`app-modules/service-management/database/migrations/2026_08_05_074634_create_service_monitoring_target_report_user_table.php`, model `app-modules/service-management/src/Models/ServiceMonitoringTargetReportUser.php`)
    - `service_monitoring_target_report_department` (`app-modules/service-management/database/migrations/2026_08_05_074703_create_service_monitoring_target_report_department_table.php`, model `app-modules/service-management/src/Models/ServiceMonitoringTargetReportDepartment.php`)
    - `service_monitoring_target_report_contact` (`app-modules/service-management/database/migrations/2026_08_05_074706_create_service_monitoring_target_report_contact_table.php`, model `app-modules/service-management/src/Models/ServiceMonitoringTargetReportContact.php`)

- `app-modules/service-management/src/Models/ServiceMonitoringTarget.php` — remove `report_frequency`/`is_reporting_active`/`is_reported_via_email`/`is_reported_via_database` from `$fillable` and `$casts`, and remove the `reportUsers()`, `reportDepartments()`, `reportContacts()` relations.

- `app-modules/service-management/database/factories/ServiceMonitoringTargetFactory.php` — remove the `is_reporting_active`, `report_frequency`, `is_reported_via_database`, and `is_reported_via_email` faker attributes.

- Delete the legacy per-target report recipient factories, no longer needed once the pivot models above are deleted:
    - `app-modules/service-management/database/factories/ServiceMonitoringTargetReportUserFactory.php`
    - `app-modules/service-management/database/factories/ServiceMonitoringTargetReportDepartmentFactory.php`
    - `app-modules/service-management/database/factories/ServiceMonitoringTargetReportContactFactory.php`

- `app-modules/service-management/src/Filament/Components/AutomatedReportingSection.php` — delete the `else` branch (the legacy single-frequency schema) so `make()` always returns the per-frequency schema.

- `app-modules/service-management/src/Filament/Components/ReportChannelCheckboxList.php` — delete this class; it is only used by the legacy branch above.

- `app-modules/service-management/src/Filament/Resources/ServiceMonitorings/Pages/ViewServiceMonitoring.php` — delete the legacy "Automated Reporting" infolist section (the `else` branch of the `ServiceMonitoringReportConfigurationsFeature::active()` ternary).

- `app-modules/service-management/src/Jobs/ServiceMonitoringReportJob.php` — delete `dispatchForLegacyTargets()` and the `handle()` branch that calls it, so `handle()` always calls `dispatchForConfigurations()`.

- `app-modules/service-management/src/Jobs/ServiceMonitoringReportNotifyJob.php` — narrow the `$reportable` constructor property from `ServiceMonitoringTarget|ServiceMonitoringReportConfiguration` to `ServiceMonitoringReportConfiguration` only, remove the `report_frequency` fallback branch when resolving `$frequency`, and remove the `__unserialize()` override and its `unserializeModels` alias (these only support legacy payloads keyed under the old `serviceMonitoringTarget` property name).

- `app-modules/service-management/src/Notifications/ServiceMonitoringReportNotification.php` — search for `TODO: Cleanup Task (service-monitoring-report-configurations-feature)` and follow the instructions there.

- Delete all inactive pre-migration compatibility tests (search for `kept only until ServiceMonitoringReportConfigurationsFeature is cleaned up`):
    - `app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoringTest.php` ("report frequency is required when reporting is active and the feature is inactive")
    - `app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/EditServiceMonitoringTest.php` ("report frequency is required when reporting is active and the feature is inactive", "EditServiceMonitoring hydrates legacy report channels from persisted flags when the feature is inactive")
    - `app-modules/service-management/tests/Tenant/Jobs/ServiceMonitoringReportJobTest.php` — the `describe('the pre-migration path, kept only until ServiceMonitoringReportConfigurationsFeature is cleaned up', ...)` block
    - `app-modules/service-management/tests/Tenant/Notifications/ServiceMonitoringReportNotificationTest.php` — the two `__unserialize()` backward-compatibility tests ("restores a notification serialized under the previous payload shape with no frequency property, falling back to the target's legacy frequency", "...defaulting to Monthly when the target has no legacy frequency either")
    - `tests/TenantMigrationTests.php` — the `describe('2026_09_08_090400_tmp_backfill_service_monitoring_report_configurations', ...)` block (the temporary migration it tests is deleted above)

- `app-modules/service-management/tests/Tenant/Jobs/ServiceMonitoringReportNotifyJobTest.php` — once `$reportable` is narrowed to `ServiceMonitoringReportConfiguration` above, update every test that currently constructs `new ServiceMonitoringReportNotifyJob($serviceMonitoringTarget)` (a legacy target) to build a `ServiceMonitoringReportConfiguration` instead, matching the configuration-backed tests already in this file, and delete the `__serialize()`/`__unserialize()` backward-compatibility test ("restores a job serialized under the previous serviceMonitoringTarget property name") along with the shim it exercises.

- Delete this file (`.cleanup-tasks/2026_09_08_service_monitoring_report_configurations_feature.md`) once all the above cleanup is complete.
