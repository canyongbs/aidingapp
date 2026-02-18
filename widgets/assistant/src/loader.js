/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
(function () {
    const scriptTag = document.currentScript;
    if (!scriptTag) throw new Error('Could not find script tag');

    const configUrl = scriptTag.getAttribute('data-config');
    if (!configUrl) throw new Error('Config URL not found in script tag');

    fetch(configUrl)
        .then((response) => response.json())
        .then((config) => {
            if (!config || !config.asset_url || !config.js) {
                throw Error('Config is missing or incomplete.');
            }

            window.__VITE_ASSISTANT_WIDGET_ASSET_URL__ = config.asset_url;
            window.__ASSISTANT_WIDGET_CONFIG__ = config;

            if (!document.querySelector('assistant-widget-embed')) {
                const embedElement = document.createElement('assistant-widget-embed');
                embedElement.id = 'assistant-widget-root';
                document.body.appendChild(embedElement);
            }

            const scriptElement = document.createElement('script');
            scriptElement.src = config.js;
            scriptElement.type = 'module';
            document.body.appendChild(scriptElement);
        })
        .catch((error) => {
            console.error('Failed to load assistant widget:', error);
        });
})();
