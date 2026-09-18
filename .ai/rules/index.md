# Rules Index

This file maps file globs to rule files. Before creating or editing any file, read every rule file whose globs cover the path(s) in scope, and `grep -rin 'keyword' .ai/rules` to catch anything a path match alone misses.

| Rule file | Applies to (globs) |
| --- | --- |
| [scheduler.md](scheduler.md) | `app/Console/Kernel.php`, `app/Jobs/DispatchForEachTenant.php`, `**/Jobs/Dispatch*ForEachTenant.php` |
