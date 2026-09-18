# Scheduler: landlord-level fan-out

**Globs:** `app/Console/Kernel.php`, `app/Jobs/DispatchForEachTenant.php`, `**/Jobs/Dispatch*ForEachTenant.php`

The scheduler is a **flat, landlord-level** schedule. It does **not** loop over tenants and does **not** use `tenants:artisan`. Per-tenant recurring work must be a **fan-out orchestrator** extending `App\Jobs\DispatchForEachTenant`, scheduled once with `->onOneServer()`; the orchestrator enqueues one child job per eligible tenant.

Two traps that cost real debugging time:

- **`onOneServer` mutex-name collision:** `Schedule::job()` derives the mutex from the job class name, so two scheduled entries of the **same class** share one mutex and only one runs. Give each same-class entry a distinct `->name()`.
- **Tenant-aware job deleted at the landlord level:** a queued job without `NotTenantAware` dispatched with no current tenant (the scheduler context) is **deleted before `handle()`** by multitenancy. Orchestrators are safe (`DispatchForEachTenant` is `NotTenantAware`) and child jobs are safe (dispatched inside `$tenant->execute()`), but a vendor/queued job you schedule directly must be added to `config/multitenancy.php` `not_tenant_aware_jobs`.

For the full how-to — the `DispatchForEachTenant` contract, `jobForTenant()`, parameterized `uniqueId()`, and testing — follow the **`scheduling-tenant-work`** skill.
