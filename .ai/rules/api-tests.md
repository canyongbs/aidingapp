---
paths:
    - '**/tests/**/Http/Controllers/Api/**'
---

# API Tests

## Use actingAsApiUser() in API tests

Authenticate programmatic users in API tests with `Tests\actingAsApiUser($systemUser)` (tests/Helpers.php), never `Sanctum::actingAs()`. The helper issues a real API token the way the app does; Sanctum's mocked token cannot be serialized by the queued audit listener, which is why older tests disabled auditing. Do not disable `audit.enabled` or `audit.queue.enable` in API tests.
