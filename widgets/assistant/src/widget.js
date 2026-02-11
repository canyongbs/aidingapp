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
import { createApp } from 'vue';
import App from './App.vue';
import styles from './widget.css?inline';

const config = window.__ASSISTANT_WIDGET_CONFIG__;

if (!config || !config.send_message_url || !config.websockets_config) {
    console.error('Assistant widget: Configuration is missing or incomplete.');
} else {
    const widgetRoot = document.createElement('div');
    widgetRoot.id = 'assistant-widget-root';
    document.body.appendChild(widgetRoot);

    const shadowHost = document.createElement('div');
    widgetRoot.appendChild(shadowHost);

    const shadowRoot = shadowHost.attachShadow({ mode: 'open' });

    const styleSheet = document.createElement('style');
    styleSheet.textContent = styles;
    shadowRoot.appendChild(styleSheet);

    const appContainer = document.createElement('div');
    shadowRoot.appendChild(appContainer);

    const app = createApp(App, {
        sendMessageUrl: config.send_message_url,
        websocketsConfig: config.websockets_config,
        primaryColor: config.primary_color,
        rounding: config.rounding,
        isAuthenticated: config.is_authenticated || false,
        guestTokenEnabled: config.guest_token_enabled || false,
    });

    app.mount(appContainer);
}
