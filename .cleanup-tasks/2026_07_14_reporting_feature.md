---
title: Reporting Feature
created: 2026-07-14
---

## Feature Flags

- `App\Features\ReportingFeature`

## Temporary Migrations

- The feature flag activation in `app-modules/report/database/migrations/2026_07_13_000004_data_activate_reporting_feature.php` (the `ReportingFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up.

## Additional Cleanup

- In `app-modules/report/src/Abstract/EngagementReport.php`: Remove the `ReportingFeature::active()` check — always use `ReportAccessKey::fromPageClass(static::class)?->userCanAccess($user) ?? false`.

- In `app-modules/report/src/Filament/Pages/ServiceRequests.php`, `ServiceRequestFeedback.php`, `Projects.php`, `KnowledgeBase.php`, `AssetManagement.php`, `AdvisoryManagement.php`, `ContractManagement.php`, `LicenseManagement.php`, `ChangeManagement.php`, `ServiceMonitoring.php`, `AiSupportAssistant.php`, `AiClarification.php`, `AiResolution.php`: Remove the `ReportingFeature::active()` check from each `canAccess()` method — always use `ReportAccessKey::fromPageClass(static::class)?->userCanAccess($user) ?? false`.

- In `app-modules/authorization/src/Filament/Pages/Reporting.php`: Remove the `ReportingFeature::active()` check from `canAccess()` — the page should always be accessible to users with `reporting.view-any`.

- Delete the feature flag class: `app/Features/ReportingFeature.php`
