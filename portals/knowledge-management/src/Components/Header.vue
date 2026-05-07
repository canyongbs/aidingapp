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
    import { storeToRefs } from 'pinia';
    import { computed, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { globalSearchQuery } from '../Stores/globalState.js';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { useTokenStore } from '../Stores/token.js';
    import { ArrowRightEndOnRectangleIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/20/solid';

    const route = useRoute();
    const router = useRouter();
    const { user, requiresAuthentication } = storeToRefs(useAuthStore());
    const { hasServiceManagement, hasAssets, hasLicense, hasTasks } = storeToRefs(useFeatureStore());

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

    const emit = defineEmits(['showLogin']);

    const mobileMenuOpen = ref(false);

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
        },
        {
            label: 'Service',
            routeName: 'service',
            visible: hasServiceManagement && user !== null,
        },
        {
            label: 'Status',
            routeName: 'status',
            visible: user !== null,
        },
        {
            label: 'Advisories',
            routeName: 'advisories',
            visible: user !== null,
        },
        {
            label: 'Assets',
            routeName: 'assets',
            visible: hasAssets,
        },
        {
            label: 'Licenses',
            routeName: 'licenses',
            visible: hasLicense,
        },
        {
            label: 'Tasks',
            routeName: 'tasks',
            visible: hasTasks,
        },
    ]);

    const visibleMenuItems = computed(() => menuItems.value.filter((item) => item.visible !== false));

    const onSearch = () => {
        router.push({ name: 'home', query: { search: globalSearchQuery.value } });
    };
</script>

<template>
    <div class="sticky top-0 z-30 overflow-x-clip">
        <nav class="flex min-h-16 items-center bg-white px-4 shadow-xs ring-1 ring-gray-950/5">
            <!-- Mobile menu toggle -->
            <button
                type="button"
                class="relative flex size-9 items-center justify-center rounded-lg text-gray-500 outline-none transition duration-75 hover:text-gray-600 focus-visible:ring-2 focus-visible:ring-brand-600 lg:hidden"
                @click="mobileMenuOpen = !mobileMenuOpen"
            >
                <svg v-if="!mobileMenuOpen" class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg v-else class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Logo (desktop) -->
            <div class="me-6 hidden items-center lg:flex">
                <router-link :to="{ name: 'home' }" class="ms-3">
                    <img :src="headerLogo" :alt="appName" class="h-9 block" />
                </router-link>
            </div>

            <!-- Nav items (desktop) -->
            <ul class="ms-4 me-4 hidden items-center gap-x-4 lg:my-2 lg:flex lg:flex-wrap lg:gap-y-1">
                <li v-for="item in visibleMenuItems" :key="item.label">
                    <router-link
                        :to="{ name: item.routeName }"
                        custom
                        v-slot="{ navigate, isActive, isExactActive }"
                    >
                        <a
                            @click="navigate"
                            class="flex items-center justify-center gap-x-2 rounded-lg px-3 py-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 cursor-pointer"
                            :class="(isActive || isExactActive) && 'bg-gray-50'"
                        >
                            <span
                                class="text-sm font-medium"
                                :class="isActive || isExactActive ? 'text-brand-600' : 'text-gray-700'"
                            >
                                {{ item.label }}
                            </span>
                        </a>
                    </router-link>
                </li>
            </ul>

            <!-- End section -->
            <div class="ms-auto flex items-center gap-x-4">
                <!-- Global search -->
                <form v-if="!['home', 'view-category'].includes(route.name)" @submit.prevent="onSearch" class="flex items-center">
                    <div class="flex rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus-within:ring-2 focus-within:ring-brand-600">
                        <div class="flex items-center gap-x-3 ps-3 pe-2">
                            <svg class="size-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <input
                                v-model="globalSearchQuery"
                                type="search"
                                autocomplete="off"
                                placeholder="Search"
                                class="block w-full appearance-none border-none bg-transparent ps-0 px-3 py-1.5 text-start text-sm leading-6 text-gray-950 placeholder:text-gray-400 focus:ring-0 focus:outline-none"
                            />
                        </div>
                    </div>
                </form>

                <!-- Sign in / Sign out -->
                <div v-if="requiresAuthentication || hasServiceManagement">
                    <button
                        v-if="user"
                        type="button"
                        @click="logout"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-2 focus-visible:ring-brand-500/50"
                    >
                        <ArrowRightStartOnRectangleIcon class="size-5" />
                        Sign out
                    </button>
                    <button
                        v-else
                        type="button"
                        @click="emit('showLogin')"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-2 focus-visible:ring-brand-500/50"
                    >
                        <ArrowRightEndOnRectangleIcon class="size-5" />
                        Sign in
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile nav menu -->
        <div
            v-if="mobileMenuOpen"
            class="border-t border-gray-200 bg-white px-4 py-3 shadow-xs ring-1 ring-gray-950/5 lg:hidden"
        >
            <ul class="flex flex-col gap-y-1">
                <li v-for="item in visibleMenuItems" :key="item.label">
                    <router-link
                        :to="{ name: item.routeName }"
                        custom
                        v-slot="{ navigate, isActive, isExactActive }"
                    >
                        <a
                            @click="navigate(); mobileMenuOpen = false"
                            class="flex items-center gap-x-2 rounded-lg px-3 py-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 cursor-pointer"
                        >
                            <span
                                class="text-sm font-medium"
                                :class="isActive || isExactActive ? 'text-brand-600' : 'text-gray-700'"
                            >
                                {{ item.label }}
                            </span>
                        </a>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>
