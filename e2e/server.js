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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

import { createReadStream, existsSync, readFileSync } from 'fs';
import { createServer } from 'http';
import { dirname, extname, resolve } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT_DIR = resolve(__dirname, '..');
const PUBLIC_DIR = resolve(ROOT_DIR, 'public');
const PORT = parseInt(process.env.E2E_PORT ?? '9999', 10);

const MIME = {
    '.html': 'text/html; charset=utf-8',
    '.js': 'application/javascript',
    '.mjs': 'application/javascript',
    '.css': 'text/css',
    '.json': 'application/json',
    '.svg': 'image/svg+xml',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.webp': 'image/webp',
    '.woff': 'font/woff',
    '.woff2': 'font/woff2',
    '.ttf': 'font/ttf',
    '.map': 'application/json',
};

function buildHarness(bundleFile) {
    const base = 'http://localhost:' + PORT;
    return [
        '<!DOCTYPE html>',
        '<html lang="en">',
        '<head>',
        '  <meta charset="UTF-8">',
        '  <meta name="viewport" content="width=device-width, initial-scale=1.0">',
        '  <title>Portal E2E Test Harness</title>',
        '</head>',
        '<body>',
        '  <knowledge-management-portal-embed',
        '    url="' + base + '/api/portal"',
        '    user-authentication-url="' + base + '/api/user"',
        '    access-url="' + base + '/portal"',
        '    search-url="' + base + '/api/portal/search"',
        '    app-url="' + base + '"',
        '    api-url="' + base + '/api/portal"',
        '  ></knowledge-management-portal-embed>',
        '  <script src="/js/portals/knowledge-management/' + bundleFile + '" type="module"></script>',
        '</body>',
        '</html>',
    ].join('\n');
}

function resolveBundle() {
    const manifestPath = resolve(PUBLIC_DIR, 'js/portals/knowledge-management/.vite/manifest.json');
    if (!existsSync(manifestPath)) {
        throw new Error(
            'Portal manifest not found at ' + manifestPath + '\nBuild the portal first: npm run build:portals',
        );
    }
    const manifest = JSON.parse(readFileSync(manifestPath, 'utf-8'));
    return manifest['src/portal.js'].file;
}

const bundleFile = resolveBundle();
const harness = buildHarness(bundleFile);

const server = createServer((req, res) => {
    const urlPath = (req.url ?? '/').split('?')[0];

    if (urlPath === '/portal' || urlPath === '/portal/') {
        res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
        res.end(harness);
        return;
    }

    const filePath = resolve(PUBLIC_DIR, '.' + urlPath);

    if (!filePath.startsWith(PUBLIC_DIR + '/')) {
        res.writeHead(403);
        res.end('Forbidden');
        return;
    }

    if (!existsSync(filePath)) {
        res.writeHead(404);
        res.end('Not found');
        return;
    }

    const contentType = MIME[extname(filePath)] ?? 'application/octet-stream';
    res.writeHead(200, {
        'Content-Type': contentType,
        'Access-Control-Allow-Origin': '*',
    });
    createReadStream(filePath).pipe(res);
});

server.listen(PORT, () => {
    process.stdout.write('E2E test server listening on http://localhost:' + PORT + '\n');
});
