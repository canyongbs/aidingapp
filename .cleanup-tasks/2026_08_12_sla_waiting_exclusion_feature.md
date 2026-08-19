---
title: Sla Waiting Exclusion Feature
created: 2026-08-12
---

## Feature Flags

- App\Features\SlaWaitingExclusionFeature

## Temporary Migrations

- app-modules/service-management/database/migrations/2026_08_12_163559_tmp_backfill_service_request_status_periods.php

## Additional Cleanup

- `app/Features/SlaWaitingExclusionFeature.php` — delete the whole file.

- `app-modules/service-management/src/Models/ServiceRequest.php`
    - In `getResolutionSeconds()`, remove the `if (SlaWaitingExclusionFeature::active())` guard so the `$seconds -= $this->getExcludedSecondsBetween(...)` subtraction (excluding `Waiting` and `Closed` time) always runs.
    - In `responseSecondsBetween()`, remove the `if (SlaWaitingExclusionFeature::active())` guard so the `$seconds -= $this->getExcludedSecondsBetween(...)` subtraction (excluding `Waiting` time) always runs.
    - Remove the `use App\Features\SlaWaitingExclusionFeature;` import.
- `app-modules/service-management/src/Observers/ServiceRequestObserver.php`
    - In `saving()`, drop the `SlaWaitingExclusionFeature::active() &&` part of the condition so it becomes `if ($serviceRequest->exists)`. Keep the `elseif (is_null($serviceRequest->time_to_resolution))` branch — it still handles the not-yet-persisted record case.
    - Remove the `use App\Features\SlaWaitingExclusionFeature;` import.

- Delete the tests that cover the inactive (pre-migration) branch, since that branch no longer exists. Each also needs its `use App\Features\SlaWaitingExclusionFeature;` import removed.
    - `app-modules/service-management/tests/Tenant/Models/ServiceRequestTest.php` — `it('includes waiting and closed time in the resolution seconds when the feature is inactive')`.
    - `app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/EditServiceRequestTest.php` — `test('check if time to resolution has correct value when status is changed')` (it calls `SlaWaitingExclusionFeature::deactivate()` to assert the legacy time-to-resolution calculation).

- Delete this file (`.cleanup-tasks/2026_08_12_sla_waiting_exclusion_feature.md`) once all the above cleanup is complete.
