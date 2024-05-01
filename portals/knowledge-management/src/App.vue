<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
-->
<script setup>
    import { defineProps, onMounted, ref, watch } from 'vue';
    import AppLoading from './Components/AppLoading.vue';
    import MobileSidebar from './Components/MobileSidebar.vue';
    import DesktopSidebar from './Components/DesktopSidebar.vue';
    import determineIfUserIsAuthenticated from './Services/DetermineIfUserIsAuthenticated.js';
    import getAppContext from './Services/GetAppContext.js';
    import axios from './Globals/Axios.js';
    import { useTokenStore } from './Stores/token.js';
    import { useAuthStore } from './Stores/auth.js';
    import { useRoute } from 'vue-router';
    import { useFeatureStore } from './Stores/feature.js';
    import { consumer } from './Services/Consumer.js';

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
    });

    const errorLoading = ref(false);
    const loading = ref(true);
    const showMobileMenu = ref(false);
    const userIsAuthenticated = ref(false);
    const requiresAuthentication = ref(false);
    const hasServiceManagement = ref(false);
    const showLogin = ref(false);

    const portalPrimaryColor = ref('');
    const portalRounding = ref('');
    const categories = ref({});
    const serviceRequests = ref({});

    const authentication = ref({
        code: null,
        email: null,
        isRequested: false,
        requestedMessage: null,
        requestUrl: null,
        url: null,
    });

    const scriptUrl = new URL(document.currentScript.getAttribute('src'));
    const protocol = scriptUrl.protocol;
    const scriptHostname = scriptUrl.hostname;

    const hostUrl = `${protocol}//${scriptHostname}`;

    const route = useRoute();

    onMounted(async () => {
        const { isEmbeddedInAidingApp } = getAppContext(props.accessUrl);

        if (isEmbeddedInAidingApp) {
            await axios.get(props.appUrl + '/sanctum/csrf-cookie');
        }

        await determineIfUserIsAuthenticated(props.userAuthenticationUrl).then((response) => {
            userIsAuthenticated.value = response;
        });

        await getKnowledgeManagementPortal().then(async () => {
            const { requiresAuthentication } = useAuthStore();

            if (userIsAuthenticated.value || !requiresAuthentication) {
                await getData();
                return;
            }
            loading.value = false;
        });
    });

    watch(
        route,
        function () {
            getKnowledgeManagementPortal();
        },
        {
            immediate: true,
        },
    );

    async function getKnowledgeManagementPortal() {
        await axios
            .get(props.url)
            .then((response) => {
                errorLoading.value = false;

                if (response.error) {
                    throw new Error(response.error);
                }

                const { setRequiresAuthentication } = useAuthStore();

                const { setHasServiceManagement } = useFeatureStore();

                serviceRequests.value = response.data.service_requests;

                portalPrimaryColor.value = response.data.primary_color;

                setRequiresAuthentication(response.data.requires_authentication).then(() => {
                    requiresAuthentication.value = response.data.requires_authentication;
                });

                setHasServiceManagement(response.data.service_management_enabled).then(() => {
                    hasServiceManagement.value = response.data.service_management_enabled;
                });

                authentication.value.requestUrl = response.data.authentication_url ?? null;

                portalRounding.value = {
                    none: {
                        sm: '0px',
                        default: '0px',
                        md: '0px',
                        lg: '0px',
                        full: '0px',
                    },
                    sm: {
                        sm: '0.125rem',
                        default: '0.25rem',
                        md: '0.375rem',
                        lg: '0.5rem',
                        full: '9999px',
                    },
                    md: {
                        sm: '0.25rem',
                        default: '0.375rem',
                        md: '0.5rem',
                        lg: '0.75rem',
                        full: '9999px',
                    },
                    lg: {
                        sm: '0.375rem',
                        default: '0.5rem',
                        md: '0.75rem',
                        lg: '1rem',
                        full: '9999px',
                    },
                    full: {
                        sm: '9999px',
                        default: '9999px',
                        md: '9999px',
                        lg: '9999px',
                        full: '9999px',
                    },
                }[response.data.rounding ?? 'md'];
            })
            .catch((error) => {
                errorLoading.value = true;
                console.error(`Help Center Embed ${error}`);
            });
    }

    async function getData() {
        await Promise.all([getKnowledgeManagementPortalCategories(), getServiceRequests()])
            .then((responses) => {
                errorLoading.value = false;

                if (responses[0].error) {
                    throw new Error(responses[0].error);
                }
                categories.value = responses[0];

                if (responses[1].error) {
                    throw new Error(responses[1].error);
                }
                serviceRequests.value = responses[1];

                loading.value = false;
            })
            .catch((error) => {
                errorLoading.value = true;
                console.error(`Knowledge Management Portal Embed ${error}`);
            });
    }

    async function getKnowledgeManagementPortalCategories() {
        const { get } = consumer();

        return get(`${props.apiUrl}/categories`).then((response) => {
            if (response.error) {
                throw new Error(response.error);
            }

            return response.data;
        });
    }

    async function getServiceRequests() {
        const { get } = consumer();

        return get(`${props.apiUrl}/service-requests`).then((response) => {
            if (response.error) {
                throw new Error(response.error);
            }

            return response.data;
        });
    }

    async function authenticate(formData, node) {
        node.clearErrors();

        const { setToken } = useTokenStore();
        const { setUser } = useAuthStore();

        const { isEmbeddedInAidingApp } = getAppContext(props.accessUrl);

        if (isEmbeddedInAidingApp) {
            await axios.get(props.appUrl + '/sanctum/csrf-cookie');
        }

        if (authentication.value.isRequested) {
            axios
                .post(authentication.value.url, {
                    code: formData.code,
                })
                .then((response) => {
                    if (response.errors) {
                        node.setErrors([], response.errors);

                        return;
                    }

                    if (response.data.is_expired) {
                        node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                        authentication.value.isRequested = false;
                        authentication.value.requestedMessage = null;

                        return;
                    }

                    if (response.data.success === true) {
                        setToken(response.data.token);
                        setUser(response.data.user);

                        userIsAuthenticated.value = true;

                        getData();
                    }
                })
                .catch((error) => {
                    node.setErrors([error]);
                });

            return;
        }

        axios
            .post(authentication.value.requestUrl, {
                email: formData.email,
                isSpa: isEmbeddedInAidingApp,
            })
            .then((response) => {
                if (response.errors) {
                    node.setErrors([], response.errors);

                    return;
                }

                if (!response.data.authentication_url) {
                    node.setErrors([response.data.message]);

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = response.data.message;
                authentication.value.url = response.data.authentication_url;
            })
            .catch((error) => {
                node.setErrors([error]);
            });
    }
</script>

<template>
    <div
        class="font-sans bg-gray-50 min-h-screen"
        :style="{
            '--primary-50': portalPrimaryColor[50],
            '--primary-100': portalPrimaryColor[100],
            '--primary-200': portalPrimaryColor[200],
            '--primary-300': portalPrimaryColor[300],
            '--primary-400': portalPrimaryColor[400],
            '--primary-500': portalPrimaryColor[500],
            '--primary-600': portalPrimaryColor[600],
            '--primary-700': portalPrimaryColor[700],
            '--primary-800': portalPrimaryColor[800],
            '--primary-900': portalPrimaryColor[900],
            '--primary-950': portalPrimaryColor[950],
            '--rounding-sm': portalRounding.sm,
            '--rounding': portalRounding.default,
            '--rounding-md': portalRounding.md,
            '--rounding-lg': portalRounding.lg,
            '--rounding-full': portalRounding.full,
        }"
    >
        <div>
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/portals/knowledge-management/style.css'" />
        </div>

        <div v-if="loading">
            <AppLoading />
        </div>

        <div v-else>
            <div
                v-if="!userIsAuthenticated && (requiresAuthentication || showLogin)"
                class="bg-gradient flex flex-col items-center justify-center min-h-screen"
            >
                <div
                    class="max-w-md w-full bg-white rounded ring-1 ring-black/5 shadow-sm px-8 pt-6 pb-4 flex flex-col gap-6 mx-4"
                >
                    <h1 class="text-primary-950 text-center text-2xl font-semibold">Log in to Helper Center</h1>

                    <FormKit type="form" @submit="authenticate" v-model="authentication" :actions="false">
                        <FormKit
                            type="email"
                            label="Email address"
                            name="email"
                            validation="required|email"
                            validation-visibility="submit"
                            :disabled="authentication.isRequested"
                        />

                        <p v-if="authentication.requestedMessage" class="text-gray-700 font-medium text-xs my-3">
                            {{ authentication.requestedMessage }}
                        </p>

                        <FormKit
                            type="otp"
                            digits="6"
                            label="Enter the code here"
                            name="code"
                            validation="required"
                            validation-visibility="submit"
                            v-if="authentication.isRequested"
                        />

                        <div class="flex justify-between">
                            <FormKit
                                type="submit"
                                :label="authentication.isRequested ? 'Sign in' : 'Send login code'"
                            />
                            <FormKit
                                v-if="!requiresAuthentication"
                                type="button"
                                label="Cancel"
                                @click="showLogin = false"
                            />
                        </div>
                    </FormKit>
                </div>
            </div>
            <div v-else>
                <div v-if="errorLoading" class="text-center">
                    <h1 class="text-3xl font-bold text-red-500">Error Loading the Help Center</h1>
                    <p class="text-lg text-red-500">Please try again later</p>
                </div>

                <div v-else>
                    <MobileSidebar
                        v-if="showMobileMenu"
                        @show-login="showLogin = true"
                        @sidebar-closed="showMobileMenu = !showMobileMenu"
                        :categories="categories"
                        :api-url="apiUrl"
                    >
                    </MobileSidebar>

                    <DesktopSidebar @show-login="showLogin = true" :categories="categories" :api-url="apiUrl">
                    </DesktopSidebar>

                    <div class="lg:pl-72">
                        <RouterView
                            :search-url="searchUrl"
                            :api-url="apiUrl"
                            :categories="categories"
                            :service-requests="serviceRequests"
                        >
                        </RouterView>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .bg-gradient {
        @apply relative bg-no-repeat;
        background-image: radial-gradient(circle at top, theme('colors.primary.200'), theme('colors.white') 50%);
    }
</style>
