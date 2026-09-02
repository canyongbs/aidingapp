---
title: Desktop Notifications Feature
created: 2026-08-26
---

## Summary

Adds desktop (web push) notifications via `canyongbs/common`: a new
`push_subscriptions` table, the `HasBrowserNotificationSubscriptions` trait on the
`User` model, and a "Notifications" page in the profile settings cluster where users
subscribe/unsubscribe.

`App\Features\DesktopNotificationsFeature` exists only because the `push_subscriptions`
table is added by migration and this app deploys code to all tenants before each
tenant's migrations finish running. The browser notification manager checks
`DesktopNotificationsFeature::active()` before any subscription access, so a tenant
whose migration has not run yet keeps the pre-desktop-notifications behaviour instead
of querying a table that does not exist.

Remove the flag once it has been active in production for every tenant for a full
deploy cycle.

## Feature Flags

- App\Features\DesktopNotificationsFeature

## Temporary Migrations

## Additional Cleanup
