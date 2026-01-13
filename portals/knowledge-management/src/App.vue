<!--
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
-->
<script setup>
    import { FormKit } from '@formkit/vue';
    import { onMounted, ref, watch } from 'vue';
    import { RouterView, useRoute } from 'vue-router';
    import AppLoading from './Components/AppLoading.vue';
    import Assistant from './Components/Assistant.vue';
    import Footer from './Components/Footer.vue';
    import Header from './Components/Header.vue';
    import axios from './Globals/Axios.js';
    import { consumer } from './Services/Consumer.js';
    import determineIfUserIsAuthenticated from './Services/DetermineIfUserIsAuthenticated.js';
    import { useAssistantStore } from './Stores/assistant.js';
    import { useAuthStore } from './Stores/auth.js';
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

    const errorLoading = ref(false);
    const loading = ref(true);
    const userIsAuthenticated = ref(false);
    const requiresAuthentication = ref(false);
    const hasServiceManagement = ref(false);
    const hasAssets = ref(false);
    const hasLicense = ref(false);
    const hasTasks = ref(false);
    const showLogin = ref(false);

    const portalPrimaryColor = ref('');
    const portalRounding = ref('');
    const categories = ref({});
    const serviceRequests = ref({});
    const headerLogo = ref('');
    const favicon = ref('');
    const tags = ref({});
    const appName = ref('');
    const footerLogo = ref('');

    const authentication = ref({
        code: null,
        email: null,
        isRequested: false,
        requestedMessage: null,
        requestUrl: null,
        url: null,
        registrationAllowed: false,
    });

    const route = useRoute();

    onMounted(async () => {
        await determineIfUserIsAuthenticated(props.userAuthenticationUrl).then((response) => {
            userIsAuthenticated.value = response;
        });
        document.title = props.appTitle;
    });

    watch(
        route,
        async () => {
            await getKnowledgeManagementPortal().then(async () => {
                const { requiresAuthentication } = useAuthStore();

                if (userIsAuthenticated.value || !requiresAuthentication) {
                    await getData();
                    return;
                }
                loading.value = false;
            });
        },
        {
            immediate: true,
        },
    );

    watch(favicon, async (newFavicon, oldFavicon) => {
        if (newFavicon != oldFavicon) {
            var link = document.querySelector("link[rel='icon']");
            if (!link) {
                link = document.createElement('link');
                link.rel = 'icon';
                document.getElementsByTagName('head')[0].appendChild(link);
            }
            link.href = favicon.value;
        }
    });

    async function getKnowledgeManagementPortal() {
        await axios
            .get(props.url)
            .then((response) => {
                errorLoading.value = false;

                if (response.error) {
                    throw new Error(response.error);
                }

                const { setRequiresAuthentication } = useAuthStore();

                const { setHasServiceManagement, setHasAssets, setHasLicense, setHasTasks } = useFeatureStore();

                const {
                    setAssistantSendMessageUrl,
                    setSelectTypeUrl,
                    setUpdateFieldUrl,
                    setRequestUploadUrl,
                    setWebsocketsConfig,
                    setApiUrl,
                } = useAssistantStore();

                setApiUrl(props.apiUrl);

                portalPrimaryColor.value = response.data.primary_color;

                headerLogo.value = response.data.header_logo;

                favicon.value = response.data.favicon;

                appName.value = response.data.app_name;

                footerLogo.value = response.data.footer_logo;

                setRequiresAuthentication(response.data.requires_authentication).then(() => {
                    requiresAuthentication.value = response.data.requires_authentication;
                });

                setHasServiceManagement(response.data.service_management_enabled).then(() => {
                    hasServiceManagement.value = response.data.service_management_enabled;
                });

                setHasAssets(response.data.has_assets).then(() => {
                    hasAssets.value = response.data.has_assets;
                });

                setHasLicense(response.data.has_license).then(() => {
                    hasLicense.value = response.data.has_license;
                });

                setHasTasks(response.data.has_tasks).then(() => {
                    hasTasks.value = response.data.has_tasks;
                });

                setAssistantSendMessageUrl(response.data.assistant_send_message_url);
                setSelectTypeUrl(response.data.assistant_select_type_url);
                setUpdateFieldUrl(response.data.assistant_update_field_url);
                setRequestUploadUrl(response.data.assistant_request_upload_url);
                setWebsocketsConfig(response.data.websockets_config);

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
        await Promise.all([getKnowledgeManagementPortalCategories(), getTags(), getServiceRequests()])
            .then((responses) => {
                errorLoading.value = false;

                if (responses[0].error) {
                    throw new Error(responses[0].error);
                }
                categories.value = responses[0];

                if (responses[1].error) {
                    throw new Error(responses[1].error);
                }
                tags.value = responses[1];

                if (responses[2].error) {
                    throw new Error(responses[2].error);
                }
                serviceRequests.value = responses[2];

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

    async function getTags() {
        const { get } = consumer();

        return get(`${props.apiUrl}/tags`).then((response) => {
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

        const { setHasServiceManagement, setHasAssets, setHasLicense, setHasTasks } = useFeatureStore();

        const { setAssistantSendMessageUrl, setSelectTypeUrl, setUpdateFieldUrl, setRequestUploadUrl, setWebsocketsConfig, setApiUrl } =
            useAssistantStore();

        setApiUrl(props.apiUrl);

        if (authentication.value.isRequested) {
            const data = {
                code: formData.code,
            };

            if (authentication.value.registrationAllowed) {
                data = {
                    ...data,
                    email: formData.email,
                    first_name: formData.first_name,
                    last_name: formData.last_name,
                    preferred: formData.preferred,
                    mobile: formData.mobile,
                    phone: formData.phone,
                    sms_opt_out: formData.sms_opt_out,
                };
            }

            axios
                .post(authentication.value.url, data)
                .then((response) => {
                    if (response.errors) {
                        node.setErrors([], response.errors);

                        return;
                    }

                    if (response.data.is_expired) {
                        node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                        authentication.value.isRequested = false;
                        authentication.value.requestedMessage = null;
                        authentication.value.url = null;
                        authentication.value.registrationAllowed = false;

                        return;
                    }

                    if (response.data.success === true) {
                        setToken(response.data.token);
                        setUser(response.data.user);

                        setHasServiceManagement(response.data.service_management_enabled).then(() => {
                            hasServiceManagement.value = response.data.service_management_enabled;
                        });

                        setHasAssets(response.data.has_assets).then(() => {
                            hasAssets.value = response.data.has_assets;
                        });

                        setHasLicense(response.data.has_license).then(() => {
                            hasLicense.value = response.data.has_license;
                        });

                        setHasTasks(response.data.has_tasks).then(() => {
                            hasTasks.value = response.data.has_tasks;
                        });

                        setAssistantSendMessageUrl(response.data.assistant_send_message_url);
                        setSelectTypeUrl(response.data.assistant_select_type_url);
                        setUpdateFieldUrl(response.data.assistant_update_field_url);
                        setRequestUploadUrl(response.data.assistant_request_upload_url);
                        setWebsocketsConfig(response.data.websockets_config);

                        const { hasServiceManagement, hasAssets, hasLicense, hasTasks } = useFeatureStore();

                        userIsAuthenticated.value = true;

                        getData();
                    }
                })
                .catch((error) => {
                    node.setErrors([], error.response.data.errors);
                });

            return;
        }

        axios
            .post(authentication.value.requestUrl, {
                email: formData.email,
            })
            .then((response) => {
                if (!response.data.authentication_url) {
                    node.setErrors([response.data.message]);

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = response.data.message;
                authentication.value.url = response.data.authentication_url;
            })
            .catch((error) => {
                const status = error.response.status;
                const data = error.response.data;

                if (status === 404 && data.registrationAllowed === true) {
                    authentication.value.registrationAllowed = true;
                    authentication.value.isRequested = true;
                    authentication.value.requestedMessage = data.message;
                    authentication.value.url = data.authentication_url;

                    return;
                }

                node.setErrors([], data.errors);
            });
    }
</script>

<template>
    <div
        class="font-sans bg-gray-50 min-h-screen w-full max-w-full"
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
            <link rel="stylesheet" v-bind:href="cssUrl" />
        </div>
        <div v-if="loading">
            <AppLoading />
        </div>

        <div class="bg-white" v-else>
            <div
                v-if="!userIsAuthenticated && (requiresAuthentication || showLogin || route.meta.requiresAuth)"
                class="bg-gradient flex flex-col items-center justify-start min-h-screen"
            >
                <div
                    class="max-w-md w-full bg-white rounded ring-1 ring-black/5 shadow-sm px-8 pt-6 pb-4 flex flex-col gap-6 mx-4 mt-4"
                >
                    <h1 class="text-brand-950 text-center text-2xl font-semibold">Login to Help Center</h1>

                    <FormKit type="form" @submit="authenticate" v-model="authentication" :actions="false">
                        <FormKit
                            type="email"
                            label="Email address"
                            name="email"
                            validation="required|email"
                            validation-visibility="submit"
                            :disabled="authentication.isRequested || authentication.registrationAllowed"
                        />

                        <div v-if="authentication.registrationAllowed">
                            <p class="text-gray-700 font-medium text-xs my-3">
                                You are not registered yet. Please fill in the form below to register.
                            </p>

                            <FormKit
                                type="text"
                                label="First Name*"
                                name="first_name"
                                validation="required|alpha|length:0,255"
                                validation-visibility="submit"
                            />

                            <FormKit
                                type="text"
                                label="Last Name*"
                                name="last_name"
                                validation="required|alpha|length:0,255"
                                validation-visibility="submit"
                            />

                            <FormKit
                                type="text"
                                label="Preferred Name"
                                name="preferred"
                                validation="alpha|length:0,255"
                                validation-visibility="submit"
                            />

                            <FormKit
                                type="tel"
                                label="Mobile*"
                                name="mobile"
                                placeholder="xxx-xxx-xxxx"
                                validation="required|length:0,255"
                                validation-visibility="submit"
                            />

                            <FormKit
                                type="tel"
                                label="Other Phone"
                                name="phone"
                                placeholder="xxx-xxx-xxxx"
                                validation="length:0,255"
                                validation-visibility="submit"
                            />

                            <FormKit
                                type="select"
                                label="SMS Opt Out"
                                name="sms_opt_out"
                                :value="0"
                                :options="[
                                    { value: false, label: 'No' },
                                    { value: true, label: 'Yes' },
                                ]"
                                validation-visibility="submit"
                            />
                        </div>

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
            <div v-else class="min-h-screen flex flex-col">
                <Header
                    :api-url="apiUrl"
                    @show-login="showLogin = true"
                    :header-logo="headerLogo"
                    :app-name="appName"
                />

                <div v-if="errorLoading" class="text-center w-full">
                    <h1 class="text-3xl font-bold text-red-500">Error Loading the Help Center</h1>
                    <p class="text-lg text-red-500">Please try again later</p>
                </div>

                <RouterView
                    :search-url="searchUrl"
                    :api-url="apiUrl"
                    :categories="categories"
                    :service-requests="serviceRequests"
                    :tags="tags"
                    v-else
                />

                <Footer :logo="footerLogo"></Footer>

                <Assistant />
            </div>
        </div>
    </div>
</template>

<style scoped>
    .bg-gradient {
        @apply relative bg-no-repeat;
        background-image: radial-gradient(circle at top, theme('colors.brand.200'), theme('colors.white') 50%);
    }
</style>
