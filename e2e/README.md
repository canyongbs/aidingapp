# End-to-End Tests

This directory contains Playwright end-to-end tests for Aiding App frontend components.

## Structure

```
e2e/
├── server.js              # Minimal static file server used by all tests
├── portal/
│   ├── portal-fixture.js  # Shared Playwright fixture for portal tests
│   ├── homepage.spec.js   # Knowledge Management Portal homepage tests
│   └── fixtures/          # Mock API response data (JSON)
│       ├── portal-define.json      # Portal definition API response
│       ├── categories.json         # Knowledge base categories
│       ├── tags.json               # Article tags
│       └── service-requests.json  # Service requests (empty — SM disabled)
```

## How it works

Tests run in **fully standalone mode** — no backend server required.

1. `e2e/server.js` is started automatically by Playwright (`webServer` in `playwright.config.js`).  
   It serves:
    - The portal JS bundle and assets from `public/` (built output).
    - A minimal test-harness HTML page at `/portal` that embeds the `<knowledge-management-portal-embed>` custom element.

2. Each test uses the **`portalPage` fixture** (`portal-fixture.js`) which intercepts all backend API calls with `page.route()` and fulfills them from the local JSON fixture files.

3. Tests assert behaviour via Playwright's semantic locators (`getByRole`, `getByText`, `getByPlaceholder`), which automatically pierce shadow DOM — important because the portal renders inside a shadow root.

## Prerequisites

The portal JS bundle must be built before running tests:

```bash
npm run build:portals
```

## Running tests

```bash
# Run all e2e tests
npm run test:e2e

# Run portal tests only
npm run test:e2e:portal

# Open interactive Playwright UI
npm run test:e2e:ui

# View the last HTML report
npm run test:e2e:report
```

Or directly with Playwright:

```bash
npx playwright test
npx playwright test e2e/portal/homepage.spec.js
npx playwright test --headed         # show browser window
npx playwright test --debug          # pause on each step
```

## Configuration

| Environment variable | Default | Description                                                |
| -------------------- | ------- | ---------------------------------------------------------- |
| `E2E_PORT`           | `9999`  | Port for the static test server                            |
| `CI`                 | unset   | Set to any value to use CI-appropriate retries and workers |

## Adding new portal tests

Use the `portalPage` fixture to get a pre-configured page with all API mocks active:

```js
import { expect, test } from './portal-fixture.js';

test('my test', async ({ portalPage }) => {
    await expect(portalPage.getByText('something')).toBeVisible();
});
```

To test with different API responses (e.g. authentication required), override the relevant route after the fixture navigation:

```js
test('shows login when auth required', async ({ page }) => {
    await page.route('**/api/portal', (route) =>
        route.fulfill({ json: { ...define, requires_authentication: true, authentication_url: '/mock-auth' } }),
    );
    await page.goto('/portal');
    // ... assert login form is shown
});
```

## Extending to widgets and Filament

The `e2e/server.js` already serves all of `public/`, so widget bundles built by `npm run build:widgets` are also reachable. To add widget tests:

1. Create `e2e/widgets/` directory.
2. Add a `*-fixture.js` with appropriate mock routes for that widget.
3. Write spec files in the same directory.
