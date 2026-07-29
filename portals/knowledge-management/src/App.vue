<!--
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
-->
<script setup>
    import Footer from '@common/portal/Footer.vue';
    import Header from '@common/portal/Header.vue';
    import {
        CubeIcon,
        DocumentTextIcon,
        FolderIcon,
        HomeIcon,
        ShieldExclamationIcon,
        SignalIcon,
        WrenchScrewdriverIcon,
    } from '@heroicons/vue/24/outline';
    import { storeToRefs } from 'pinia';
    import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
    import { RouterView, useRoute, useRouter } from 'vue-router';
    import { useIsDataLoading } from 'vue-router/experimental';
    import BootScreen from './Components/BootScreen.vue';
    import NavigationProgress from './Components/NavigationProgress.vue';
    import { usePortalAuth } from './Composables/usePortalAuth.js';
    import { usePortalTheme } from './Composables/usePortalTheme.js';
    import Login from './Pages/Login.vue';
    import { useAuthStore } from './Stores/auth.js';
    import { useConfigStore } from './Stores/config.js';
    import { useFeatureStore } from './Stores/feature.js';
    import { useTokenStore } from './Stores/token.js';

    const props = defineProps({
        url: {
            type: String,
            required: true,
        },
        searchUrl: {
            type: String,
            required: true,
        },
        apiUrl: {
            type: String,
            required: true,
        },
        accessUrl: {
            type: String,
            required: true,
        },
        userAuthenticationUrl: {
            type: String,
            required: true,
        },
        appUrl: {
            type: String,
            required: true,
        },
        appTitle: {
            type: String,
            required: true,
        },
        cssUrl: {
            type: String,
            required: true,
            default: null,
        },
    });

    const route = useRoute();
    const router = useRouter();

    const config = useConfigStore();
    const {
        appName,
        headerLogo,
        footerLogo,
        favicon,
        assistantWidgetLoaderUrl,
        assistantWidgetConfigUrl,
        errorLoading,
    } = storeToRefs(config);

    const authStore = useAuthStore();
    const { user, requiresAuthentication, userIsAuthenticated } = storeToRefs(authStore);

    const feature = useFeatureStore();
    const {
        hasServiceManagement,
        hasAssets,
        hasLicense,
        hasProjects,
        isStatusEnabled,
        isAdvisoryEnabled,
        isAssetEnabled,
        isLicenseEnabled,
    } = storeToRefs(feature);

    const { themeStyles } = usePortalTheme();

    const { authentication, authenticate, logout } = usePortalAuth();

    const isNavigating = useIsDataLoading();

    const loading = ref(true);
    const showLogin = ref(false);

    const menuItems = computed(() =>
        [
            { label: 'Home', routeName: 'home', icon: HomeIcon },
            {
                label: 'Service',
                routeName: 'service-parent',
                icon: WrenchScrewdriverIcon,
                visible: hasServiceManagement.value && user.value !== null,
            },
            {
                label: 'Status',
                routeName: 'status',
                icon: SignalIcon,
                visible: isStatusEnabled.value && user.value !== null,
            },
            {
                label: 'Advisories',
                routeName: 'advisories',
                icon: ShieldExclamationIcon,
                visible: isAdvisoryEnabled.value && user.value !== null,
            },
            {
                label: 'Assets',
                routeName: 'assets',
                icon: CubeIcon,
                visible: isAssetEnabled.value && hasAssets.value && user.value !== null,
            },
            {
                label: 'Licenses',
                routeName: 'licenses',
                icon: DocumentTextIcon,
                visible: isLicenseEnabled.value && hasLicense.value && user.value !== null,
            },
            {
                label: 'Projects',
                routeName: 'projects',
                icon: FolderIcon,
                visible: hasProjects.value && user.value !== null,
            },
        ].filter((item) => item.visible !== false),
    );

    const showSignIn = computed(
        () =>
            !userIsAuthenticated.value && (requiresAuthentication.value || showLogin.value || route.meta?.requiresAuth),
    );

    // Hide navbar search on pages that already show their own hero search.
    const hideHeaderSearch = computed(() => ['home', 'view-category', 'view-subcategory'].includes(route.name));

    function onHeaderSearch(query) {
        router.push({ name: 'home', query: { search: query } });
    }

    const assistantWidgetLoaded = ref(false);

    function loadAssistantWidget() {
        if (!assistantWidgetLoaderUrl.value || !assistantWidgetConfigUrl.value) {
            return;
        }

        if (document.getElementById('assistant-widget-root')) {
            return;
        }

        const script = document.createElement('script');
        script.src = assistantWidgetLoaderUrl.value;
        script.setAttribute('data-config', assistantWidgetConfigUrl.value);
        document.body.appendChild(script);

        const observer = new MutationObserver(() => {
            if (document.querySelector('assistant-widget-embed')) {
                observer.disconnect();
                updateWidgetServiceManagement();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function updateWidgetServiceManagement() {
        const widget = document.querySelector('assistant-widget-embed');
        if (!widget) return;

        if (hasServiceManagement.value && userIsAuthenticated.value) {
            widget.setAttribute('portal-service-management', '');
        } else {
            widget.removeAttribute('portal-service-management');
        }
    }

    watch([() => hasServiceManagement.value, () => userIsAuthenticated.value], updateWidgetServiceManagement, {
        immediate: true,
    });

    function handleOpenServiceRequest() {
        window.dispatchEvent(new CustomEvent('assistant:close'));
        router.push({ name: 'create-service-request' });
    }

    window.addEventListener('assistant:open-service-request', handleOpenServiceRequest);

    async function handleWidgetAuthenticated(event) {
        const { token } = event.detail ?? {};
        if (!token) return;

        // Persist the token and re-boot the portal so the config + route loaders
        // refetch in the authenticated state.
        await useTokenStore().setToken(token);
        window.location.reload();
    }

    window.addEventListener('assistant-widget:authenticated', handleWidgetAuthenticated);

    onUnmounted(() => {
        window.removeEventListener('assistant:open-service-request', handleOpenServiceRequest);
        window.removeEventListener('assistant-widget:authenticated', handleWidgetAuthenticated);
    });

    watch(
        [showSignIn, assistantWidgetLoaderUrl],
        ([isSignIn]) => {
            const widgetRoot = document.getElementById('assistant-widget-root');

            if (isSignIn) {
                if (widgetRoot) {
                    widgetRoot.style.display = 'none';
                }

                return;
            }

            if (widgetRoot) {
                widgetRoot.style.display = '';
                updateWidgetServiceManagement();
            } else {
                loadAssistantWidget();
            }
        },
        { immediate: true },
    );

    watch(favicon, (newFavicon, oldFavicon) => {
        if (newFavicon && newFavicon !== oldFavicon) {
            let link = document.querySelector("link[rel='icon']");

            if (!link) {
                link = document.createElement('link');
                link.rel = 'icon';
                document.getElementsByTagName('head')[0].appendChild(link);
            }

            link.href = newFavicon;
        }
    });

    onMounted(async () => {
        if (props.appTitle) {
            document.title = props.appTitle;
        }

        try {
            await router.isReady();
        } catch {
            // Initial navigation failed (e.g. unauthenticated); the sign-in screen handles it.
        } finally {
            loading.value = false;
        }
    });
</script>

<template>
    <div class="font-sans bg-gray-50 min-h-screen w-full max-w-full" :style="themeStyles">
        <div>
            <link rel="stylesheet" v-bind:href="props.cssUrl" />
        </div>

        <BootScreen v-if="loading" label="Loading Help Center..." />

        <div v-else>
            <NavigationProgress :active="isNavigating" />

            <Login
                v-if="showSignIn"
                v-model:authentication="authentication"
                :requires-authentication="requiresAuthentication"
                :header-logo="headerLogo"
                :footer-logo="footerLogo"
                :app-name="appName"
                @authenticate="authenticate"
                @cancel="showLogin = false"
            />
            <div v-else class="min-h-screen flex flex-col">
                <Header
                    :header-logo="headerLogo"
                    :app-name="appName"
                    :user="user"
                    :requires-authentication="requiresAuthentication || hasServiceManagement"
                    :menu-items="menuItems"
                    :hide-search="hideHeaderSearch"
                    @show-login="showLogin = true"
                    @logout="logout"
                    @search="onHeaderSearch"
                />

                <main class="flex-1">
                    <div v-if="errorLoading" class="text-center w-full">
                        <h1 class="text-3xl font-bold text-red-500">Error Loading the Help Center</h1>
                        <p class="text-lg text-red-500">Please try again later</p>
                    </div>

                    <RouterView v-else v-slot="{ Component, route: activeRoute }">
                        <component :is="Component" :key="activeRoute.path" />
                    </RouterView>
                </main>

                <Footer :logo="footerLogo" :app-name="appName" />
            </div>
        </div>
    </div>
</template>
