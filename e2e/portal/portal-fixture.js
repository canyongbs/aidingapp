/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
import { test as base, expect } from '@playwright/test';
import { readFileSync } from 'fs';
import { dirname, resolve } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const FIXTURES_DIR = resolve(__dirname, 'fixtures');

const E2E_PORT = parseInt(process.env.E2E_PORT ?? '9999', 10);

export const TEST_ORIGIN = 'http://localhost:' + E2E_PORT;
export const API_BASE = TEST_ORIGIN + '/api/portal';

export const test = base.extend({
    portalPage: async ({ page }, use) => {
        const fixtures = loadFixtures();

        await page.route(API_BASE, async (route) => {
            await route.fulfill({ json: fixtures.define });
        });

        await page.route(TEST_ORIGIN + '/api/user', async (route) => {
            await route.fulfill({
                status: 401,
                contentType: 'application/json',
                body: JSON.stringify({ message: 'Unauthenticated.' }),
            });
        });

        await page.route(API_BASE + '/categories', async (route) => {
            await route.fulfill({ json: fixtures.categories });
        });

        await page.route(API_BASE + '/tags', async (route) => {
            await route.fulfill({ json: fixtures.tags });
        });

        await page.route(API_BASE + '/service-requests', async (route) => {
            await route.fulfill({ json: fixtures.serviceRequests });
        });

        await page.goto('/portal');

        await use(page);
    },
});

export { expect };

function loadFixtures() {
    return {
        define: readJson('portal-define.json'),
        categories: readJson('categories.json'),
        tags: readJson('tags.json'),
        serviceRequests: readJson('service-requests.json'),
    };
}

function readJson(filename) {
    return JSON.parse(readFileSync(resolve(FIXTURES_DIR, filename), 'utf-8'));
}
