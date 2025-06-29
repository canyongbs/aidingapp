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
import { defaultConfig, plugin } from '@formkit/vue';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import ComingSoon from './Components/ComingSoon.vue';
import Services from './Components/Services.vue';
import config from './formkit.config.js';
import Assets from './Pages/Assets.vue';
import CreateServiceRequest from './Pages/CreateServiceRequest.vue';
import Home from './Pages/Home.vue';
import Incidents from './Pages/Incidents.vue';
import Licenses from './Pages/Licenses.vue';
import SelectServiceRequestType from './Pages/SelectServiceRequestType.vue';
import ServiceMonitorStatus from './Pages/ServiceMonitorStatus.vue';
import ViewArticle from './Pages/ViewArticle.vue';
import ViewCategory from './Pages/ViewCategory.vue';
import ViewServiceRequest from './Pages/ViewServiceRequest.vue';
import './portal.css';
import getAppContext from './Services/GetAppContext.js';

customElements.define(
    'knowledge-management-portal-embed',
    defineCustomElement({
        setup(props) {
            const app = createApp();
            const pinia = createPinia();

            app.use(pinia);
            app.use(PrimeVue, {
                theme: 'none',
            });

            const { baseUrl } = getAppContext(props.accessUrl);

            const router = createRouter({
                history: createWebHistory(),
                routes: [
                    {
                        path: baseUrl + '/',
                        name: 'home',
                        component: Home,
                    },
                    {
                        path: baseUrl + '/categories/:categorySlug',
                        name: 'view-category',
                        component: ViewCategory,
                    },
                    {
                        path: baseUrl + '/categories/:parentCategorySlug/:categorySlug',
                        name: 'view-subcategory',
                        component: ViewCategory,
                    },
                    {
                        path: baseUrl + '/categories/:categorySlug/articles/:articleId',
                        name: 'view-article',
                        component: ViewArticle,
                    },
                    {
                        path: baseUrl + '/service-request-type/select',
                        name: 'create-service-request',
                        component: SelectServiceRequestType,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/service-request/create/:typeId',
                        name: 'create-service-request-from-type',
                        component: CreateServiceRequest,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/service-request/:serviceRequestId',
                        name: 'view-service-request',
                        component: ViewServiceRequest,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/services',
                        name: 'services',
                        component: Services,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/status',
                        name: 'status',
                        component: ServiceMonitorStatus,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/incidents',
                        name: 'incidents',
                        component: Incidents,
                        meta: { requiresAuth: true },
                    },
                    {
                        path: baseUrl + '/assets',
                        name: 'assets',
                        component: Assets,
                    },
                    {
                        path: baseUrl + '/licenses',
                        name: 'licenses',
                        component: Licenses,
                    },
                    {
                        path: baseUrl + '/tasks',
                        name: 'tasks',
                        component: ComingSoon,
                    },
                ],
            });

            app.use(router);

            app.config.devtools = true;

            // FormKit plugin
            app.use(plugin, defaultConfig(config));

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);

            return () => h(App, props);
        },
        props: ['url', 'userAuthenticationUrl', 'accessUrl', 'searchUrl', 'appUrl', 'apiUrl', 'tags'],
    }),
);
