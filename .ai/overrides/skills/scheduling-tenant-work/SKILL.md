---
name: scheduling-tenant-work
description: "Use when adding to or changing this app's scheduler — app/Console/Kernel.php or a *ForEachTenant orchestrator job. Trigger whenever you schedule recurring work (especially work that must run per tenant), add or edit a job extending App\\Jobs\\DispatchForEachTenant, register a $schedule->job()/command()/call() entry, or schedule a vendor/queued job at the landlord level. Covers the flat landlord-level fan-out model, the DispatchForEachTenant abstract class and its jobForTenant() contract, parameterized orchestrators with uniqueId(), the onOneServer mutex-name collision trap for same-class entries, and the tenant-aware-job-deleted-at-the-landlord-level trap (config/multitenancy.php not_tenant_aware_jobs). Do not use for writing the per-tenant business logic itself, non-scheduled jobs, general tenant-aware queue mechanics, or writing tests (use writing-tests)."
user-invocable: false
license: Elastic-2.0
metadata:
    author: canyongbs
---

# Scheduling Tenant Work

The scheduler runs **once at the landlord level**. It does **not** loop over tenants and it does **not** use `tenants:artisan`. An earlier design scheduled every task per tenant, which produced hundreds of schedule entries and made newer tenants wait behind older ones. The current model is a flat schedule of **fan-out orchestrators**: each is dispatched once, then it enqueues one child job per eligible tenant so all tenants are processed in parallel by workers.

## The model

- `app/Console/Kernel.php` holds a **flat** list of `$schedule->job(...)`, `$schedule->command(...)`, and `$schedule->call(...)` entries. Every entry is `->onOneServer()`.
- Per-tenant work is expressed as an **orchestrator** — a job extending `App\Jobs\DispatchForEachTenant` — scheduled once. The orchestrator fans out to per-tenant child jobs; it never contains the business logic itself.
- Landlord-level commands (e.g. the health heartbeats) run **once**, not per tenant.

## Adding per-tenant scheduled work

1. Create a child job that does the actual per-tenant work (a normal tenant-aware `ShouldQueue` job — no special base class). Put it in the owning module.
2. Create an orchestrator extending `App\Jobs\DispatchForEachTenant` and implement `jobForTenant()`:

```php
use App\Jobs\DispatchForEachTenant;
use App\Models\Tenant;

class DispatchPruneWidgetsForEachTenant extends DispatchForEachTenant
{
    protected function jobForTenant(Tenant $tenant): ?object
    {
        // Return null to skip this tenant (e.g. an addon/feature is off).
        return new PruneWidgetsJob();
    }
}
```

3. Register it flat in `Kernel::schedule()`:

```php
$schedule->job(new DispatchPruneWidgetsForEachTenant())
    ->hourly()
    ->onOneServer();
```

That is the whole pattern. Do not add a tenant loop, `tenants:artisan`, or per-tenant scheduling.

### What the base class already handles — don't re-implement it

`DispatchForEachTenant` is `NotTenantAware`, `ShouldQueue`, and `ShouldBeUnique`. Its `handle()`:

- scopes tenants with `SetupIsComplete` + `ExcludeExpiredSubscriptions` and cursors them (one row at a time);
- wraps each tenant in its own `try/catch` and `report()`s failures, so one tenant cannot break the run;
- calls `jobForTenant($tenant)` inside `$tenant->execute(...)` and dispatches the returned job **while the tenant is current**, so the child job is tagged with that tenant and runs tenant-aware on a worker;
- sets `onQueue(config('queue.landlord_queue'))` and a `uniqueFor` safety ceiling in its constructor.

Your `jobForTenant()` only decides **what** (if anything) to dispatch for a tenant. Return `null` to skip.

### Parameterized orchestrators (same class, several cadences)

When one orchestrator class runs at multiple frequencies (e.g. Service Monitoring), take the parameter in the constructor, call `parent::__construct()`, and give each a distinct uniqueness lock via `uniqueId()`:

```php
public function __construct(public ServiceMonitoringFrequency $frequency)
{
    parent::__construct();
}

public function uniqueId(): string
{
    return $this->frequency->value;
}
```

## Two traps (both cost real debugging time)

### 1. `onOneServer` mutex-name collision on same-class entries

`Schedule::job()` derives the `onOneServer` mutex from the job's **class name** (its description). Two scheduled entries of the **same class** therefore share one mutex and only one will run. Give every same-class entry a **distinct `->name(...)`**:

```php
$schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::FiveMinutes))
    ->everyFiveMinutes()
    ->name('Dispatch Service Monitoring For Each Tenant (Five Minutes)')
    ->onOneServer();
```

### 2. Tenant-aware jobs dispatched at the landlord level are **deleted**

This app sets `multitenancy.queues_are_tenant_aware_by_default = true`. Any queued job **without** the `NotTenantAware` interface that is dispatched with **no current tenant** (which is exactly the landlord scheduler context) is **deleted before `handle()` runs** by `MakeQueueTenantAwareAction` (it can't resolve a tenant), so its work silently never happens.

- **Orchestrators are safe** — `DispatchForEachTenant` implements `NotTenantAware`.
- **Child jobs are safe** — they're dispatched inside `$tenant->execute()`, so they carry a tenant.
- **A vendor/queued job you schedule directly at the landlord level is NOT safe.** Register it in `config/multitenancy.php` under `not_tenant_aware_jobs` (this is how `Spatie\Health\Jobs\HealthQueueJob`, dispatched by `health:queue-check-heartbeat`, is made to run at the landlord level and write the shared `health` cache store).

## Verifying

- Run the app in Docker: prefix commands with `pls exec app`.
- `pls exec app php artisan schedule:list` — confirm the flat schedule and that same-class entries have distinct names.
- Cover new cadences in `tests/Landlord/Console/KernelTest.php` (it pins every entry's minute/5/15/30/hourly/daily/weekly/monthly cadence). Give each orchestrator its own `*ForEachTenantTest` that calls `->handle()` and asserts the child job is pushed once per eligible tenant. Follow the `writing-tests` skill.
