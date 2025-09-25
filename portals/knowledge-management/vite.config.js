/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { defineConfig } from 'vite';
import { writeFileSync, mkdirSync, renameSync, existsSync } from 'fs';

export default defineConfig({
    plugins: [
        vue(),
        // {
        //     name: 'move-manifest',
        //     writeBundle() {
        //         const outDir = resolve(__dirname, '../../public/js/portals/knowledge-management');
        //         const viteManifestPath = resolve(outDir, '.vite/manifest.json');
        //         const targetManifestPath = resolve(outDir, 'manifest.json');

        //         if (existsSync(viteManifestPath)) {
        //             // Ensure target directory exists
        //             mkdirSync(outDir, { recursive: true });

        //             // Move manifest to root of outDir
        //             renameSync(viteManifestPath, targetManifestPath);

        //             // Remove the .vite directory
        //             try {
        //                 const { rmSync } = require('fs');
        //                 rmSync(resolve(outDir, '.vite'), { recursive: true, force: true });
        //             } catch (e) {
        //                 // Ignore errors if .vite directory doesn't exist or can't be removed
        //             }
        //         }
        //     }
        // }
    ],
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                portal: resolve(__dirname, './src/portal.js'),
                loader: resolve(__dirname, './src/loader.js')
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    return chunkInfo.name === 'loader'
                        ? 'aiding-app-knowledge-management-loader.js'
                        : 'aiding-app-knowledge-management-portal-[hash].js';
                },
                assetFileNames: 'aiding-app-knowledge-management-portal-[hash].css'
            }
        },
        outDir: resolve(__dirname, '../../public/js/portals/knowledge-management'),
        emptyOutDir: true,
        sourcemap: true,
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
        },
    },
    define: { 'process.env.NODE_ENV': '"production"' },
});
