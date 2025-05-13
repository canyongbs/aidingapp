<!--
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
-->
<script setup>
    import { ArrowRightEndOnRectangleIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/24/outline';
    import Menubar from 'primevue/menubar';
    import { computed, defineProps, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { useTokenStore } from '../Stores/token.js';
    import GlobalSearchBar from './GlobalSearchBar.vue';
    import MobileMenu from './MobileMenu.vue';

    const route = useRoute();
    const router = useRouter();
    const { user, requiresAuthentication } = useAuthStore();
    const { hasServiceManagement, hasAssets, hasLicense, hasTasks } = useFeatureStore();

    const { removeToken } = useTokenStore();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
        headerLogo: {
            type: String,
            required: true,
        },
        appName: {
            type: String,
            required: true,
        },
    });

    const logout = () => {
        const { post } = consumer();

        post(props.apiUrl + '/logout').then((response) => {
            if (!response.data.success) {
                return;
            }

            removeToken();
            window.location.href = response.data.redirect_url;
        });
    };

    const menuItems = ref([
        {
            label: 'Home',
            routeName: 'home',
            command: () => router.push({ name: 'home' }),
        },
        {
            label: 'Service',
            routeName: 'services',
            visible: hasServiceManagement && user !== null,
            command: () => router.push({ name: 'services' }),
        },
        {
            label: 'Status',
            routeName: 'status',
            visible: user !== null,
            command: () => router.push({ name: 'status' }),
        },
        {
            label: 'Incidents',
            routeName: 'incidents',
            visible: user !== null,
            command: () => router.push({ name: 'incidents' }),
        },
        {
            label: 'Assets',
            routeName: 'assets',
            visible: hasAssets,
            command: () => router.push({ name: 'assets' }),
        },
        {
            label: 'Licenses',
            routeName: 'licenses',
            visible: hasLicense,
            command: () => router.push({ name: 'licenses' }),
        },
        {
            label: 'Tasks',
            routeName: 'tasks',
            visible: hasTasks,
            command: () => router.push({ name: 'tasks' }),
        },
    ]);

    const visibleMenuItems = computed(() => menuItems.value.filter((item) => item.visible !== false));
</script>

<template>
    <Menubar class="border border-none md:px-1.5 xl:px-0">
        <template #start>
            <router-link :to="{ name: 'home', query: {} }" class="flex items-center">
                <img :src="headerLogo" :alt="appName" class="h-12 m-0" />
            </router-link>
        </template>

        <template #end>
            <div class="flex items-center gap-6 ml-auto">
                <div class="flex items-center gap-10 hidden lg:flex">
                    <template v-for="item in visibleMenuItems" :key="item.label">
                        <router-link
                            :to="{ name: item.routeName }"
                            custom
                            v-slot="{ navigate, isActive, isExactActive }"
                        >
                            <a
                                @click="navigate"
                                class="cursor-pointer flex items-center font-medium text-sm"
                                :class="[
                                    isActive || isExactActive ? 'text-brand-500' : 'text-gray-700',
                                    'hover:text-brand-500',
                                ]"
                            >
                                {{ item.label }}
                            </a>
                        </router-link>
                    </template>
                </div>

                <div class="flex items-center gap-4">
                    <GlobalSearchBar
                        v-if="route.name === 'view-article' || route.name === 'services' || route.name === 'licenses'"
                    ></GlobalSearchBar>

                    <div v-if="requiresAuthentication || hasServiceManagement">
                        <button
                            v-if="user"
                            @click="logout"
                            type="button"
                            class="flex items-center bg-gradient-to-br from-brand-500 to-brand-800 text-white text-sm font-medium px-3 py-2 rounded"
                        >
                            <ArrowRightStartOnRectangleIcon class="h-5 w-5 mr-1" />
                            <span class="mr-2">Sign out</span>
                        </button>
                        <button
                            v-else
                            @click="$emit('showLogin')"
                            type="button"
                            class="flex items-center bg-gradient-to-br from-brand-500 to-brand-800 text-white text-sm font-medium px-3 py-2 rounded"
                        >
                            <ArrowRightEndOnRectangleIcon class="h-5 w-5 mr-1" />
                            <span>Sign in</span>
                        </button>
                    </div>
                    <MobileMenu class="relative lg:hidden" :visibleMenuItems="visibleMenuItems" />
                </div>
            </div>
        </template>
    </Menubar>
</template>

<style scoped>
    .gap-6 > * {
        white-space: nowrap;
    }

    .text-transparent.bg-clip-text {
        -webkit-background-clip: text;
        background-clip: text;
    }

    .pointer {
        cursor: pointer;
    }
</style>
