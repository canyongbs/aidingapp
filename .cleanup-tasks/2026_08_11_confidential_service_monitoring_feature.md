---
title: Confidential Service Monitoring Feature
created: 2026-08-11
---

## Summary

Adds confidentiality to service monitors: an `is_confidential` flag, a `created_by`
morph, and three grant pivot tables (`service_monitoring_target_confidential_user`,
`..._department`, `..._contact`) that restrict visibility to admins, the creator,
and explicitly granted users/departments/contacts.

`App\Features\ConfidentialServiceMonitoringFeature` exists only because those
columns/tables are added by migration and this app deploys code to all tenants
before each tenant's migrations finish running. Every place that reads or writes
the new columns/relationships checks `ConfidentialServiceMonitoringFeature::active()`
first, so a tenant whose migration hasn't run yet keeps the pre-confidentiality
behaviour instead of hitting a "column/table does not exist" error.

Remove the flag once it has been active in production for every tenant for a
full deploy cycle.

## Feature Flags

- App\Features\ConfidentialServiceMonitoringFeature

## Temporary Migrations

## Additional Cleanup

- Search for `TODO: Cleanup Task (confidential-service-monitoring)` and follow the instructions at each site.
- The following files branch on `ConfidentialServiceMonitoringFeature::active()` — keep the active branch and delete the inactive branch/early-return in each:
    - `app-modules/service-management/src/Models/Scopes/ServiceMonitoringTargetVisibilityScope.php` (`apply()` early return)
    - `app-modules/service-management/src/Observers/ServiceMonitoringTargetObserver.php` (`creating()`/`updating()` early returns)
    - `app-modules/service-management/src/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoring.php` (conditional `ConfidentialitySection::make()`)
    - `app-modules/service-management/src/Filament/Resources/ServiceMonitorings/Pages/EditServiceMonitoring.php` (conditional `ConfidentialitySection::make()` and `afterSave()` early return)
    - `app-modules/service-management/src/Filament/Resources/ServiceMonitorings/Pages/ViewServiceMonitoring.php` (Confidentiality infolist section `visible()`)
    - `app-modules/service-management/src/Filament/Resources/ServiceMonitorings/Pages/ListServiceMonitorings.php` (name column `icon()`/`tooltip()`)
    - `app-modules/service-management/src/Notifications/ServiceMonitoringNotification.php` (`notifiableCanViewTarget()` early return)
- Delete the activation migration `app-modules/service-management/database/migrations/2026_08_11_120955_data_activate_confidential_service_monitoring_feature.php` along with the flag class.
