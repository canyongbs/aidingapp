---
title: Desktop Notifications Feature
created: 2026-08-26
---

## Summary

Adds desktop (web push) notifications via the `emuniq/filament-browser-notifications`
package: a new `push_subscriptions` table, the `HasPushSubscriptions` trait on the
`User` model, and a "Notifications" page in the profile settings cluster where users
subscribe/unsubscribe.

`App\Features\DesktopNotificationsFeature` exists only because the `push_subscriptions`
table is added by migration and this app deploys code to all tenants before each
tenant's migrations finish running. The notifications management page checks
`DesktopNotificationsFeature::active()` first, so a tenant whose migration hasn't run
yet keeps the pre-desktop-notifications behaviour instead of exposing a page backed by
a table that does not exist.

Remove the flag once it has been active in production for every tenant for a full
deploy cycle.

## Feature Flags

- App\Features\DesktopNotificationsFeature

## Temporary Migrations

## Additional Cleanup

- The following file branches on `DesktopNotificationsFeature::active()` — keep the active branch and delete the inactive branch in each:
    - `app/Filament/Pages/ManageBrowserNotifications.php` (`canAccess()`)
- Remove the activation of `DesktopNotificationsFeature` from `database/migrations/2026_08_25_130000_create_push_subscriptions_table.php` when deleting the flag class.
