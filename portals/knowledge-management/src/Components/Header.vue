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
            visible: hasServiceManagement,
            command: () => router.push({ name: 'services' }),
        },
        {
            label: 'Incidents',
            routeName: 'incidents',
            command: () => router.push({ name: 'incidents' }),
        },
        {
            label: 'Knowledge Base',
            routeName: 'knowledge-base',
            command: () => router.push({ name: 'knowledge-base' }),
        },
        {
            label: 'Assets',
            routeName: 'assets',
            visible: hasAssets,
            command: () => router.push({ name: 'assets' }),
        },
        {
            label: 'Licenses',
            routeName: 'license',
            visible: hasLicense,
            command: () => router.push({ name: 'license' }),
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
            <router-link :to="{ name: 'home' }" class="flex items-center">
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
                    <GlobalSearchBar v-if="route.name === 'view-article'"></GlobalSearchBar>

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
