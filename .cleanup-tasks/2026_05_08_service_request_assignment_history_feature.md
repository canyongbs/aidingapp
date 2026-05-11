---
title: Service Request Assignment History Feature
created: 2026-05-08
---

## Feature Flags

- App\Features\ServiceRequestAssignmentHistoryFeature

## Temporary Migrations

## Additional Cleanup

- Remove the feature-flag gate in `AssignmentHistoryRelationManager::canViewForRecord()` (`app-modules/service-management/src/Filament/Resources/ServiceRequests/RelationManagers/AssignmentHistoryRelationManager.php`) so the History section always renders.
- Remove the feature-flag check around the status snapshot in `ServiceRequestAssignmentObserver::creating()` (`app-modules/service-management/src/Observers/ServiceRequestAssignmentObserver.php`) so every new assignment captures `service_request_status_id` unconditionally.
- Remove the `ServiceRequestAssignmentHistoryFeature::activate()` / `deactivate()` calls from migration `app-modules/service-management/database/migrations/2026_05_07_173737_add_service_request_status_id_to_service_request_assignments_table.php` before deleting the feature-flag class.
