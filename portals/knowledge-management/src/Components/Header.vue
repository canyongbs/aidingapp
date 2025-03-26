<template>
    <Menubar class="border border-none px-0 hidden lg:flex">
        <!-- Logo -->
        <template #start>
            <router-link :to="{ name: 'home' }" class="flex items-center">
                <img :src="headerLogo" :alt="appName" class="h-12 m-0" />
            </router-link>
        </template>

        <!-- Desktop Menu Items -->
        <template #end>
            <div class="flex items-center gap-6 ml-auto">
                <div class="flex items-center gap-6">
                    <template v-for="item in visibleMenuItems" :key="item.label">
                        <a
                            v-ripple
                            class="flex items-center cursor-pointer text-sm font-medium"
                            :class="{
                                'text-transparent bg-clip-text bg-gradient-to-br from-brand-500 to-brand-800':
                                    isActive(item),
                                'text-gray-700 hover:text-brand-500': !isActive(item),
                            }"
                            @click="item.command"
                        >
                            <i :class="item.icon" class="mr-2"></i>
                            <span>{{ item.label }}</span>
                        </a>
                    </template>
                </div>

                <!-- Global Search Bar if required -->
                <GlobalSearchBar v-if="showSearchBar" class="mr-4" />

                <!-- Auth Buttons -->
                <div v-if="requiresAuthentication || hasServiceManagement">
                    <button
                        v-if="user"
                        @click="logout"
                        type="button"
                        class="bg-gradient-to-br from-brand-500 to-brand-800 text-white text-sm font-medium px-4 py-2 rounded"
                    >
                        Sign out
                    </button>
                    <button
                        v-else
                        @click="$emit('showLogin')"
                        type="button"
                        class="bg-gradient-to-br from-brand-500 to-brand-800 text-white text-sm font-medium px-4 py-2 rounded"
                    >
                        Sign in
                    </button>
                </div>
            </div>
        </template>
    </Menubar>

    <!-- Mobile Navigation (Hidden in Desktop) -->
    <div class="lg:hidden flex items-center justify-between px-4 py-2">
        <router-link :to="{ name: 'home' }" class="flex items-center">
            <img :src="headerLogo" :alt="appName" class="h-10 m-0" />
        </router-link>
        <button @click="toggleDrawer" class="text-gray-700 focus:outline-none">
            <i class="pi pi-bars text-2xl"></i>
        </button>
    </div>

    <!-- Drawer for Mobile Menu -->
    <Drawer v-model:visible="drawerVisible" position="right" class="w-64" modal :showCloseIcon="false">
        <div class="p-4 flex justify-between items-center border-b border-gray-200">
            <span class="text-lg font-semibold">Menu</span>
            <button @click="toggleDrawer" class="text-gray-700">
                <i class="pi pi-times text-xl"></i>
            </button>
        </div>

        <!-- PanelMenu in Drawer -->
        <PanelMenu :model="panelMenuItems" class="p-4" />
    </Drawer>
</template>

<script setup>
    import Drawer from 'primevue/drawer';
    import Menubar from 'primevue/menubar';
    import PanelMenu from 'primevue/panelmenu';
    import { computed, defineProps, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { useTokenStore } from '../Stores/token.js';

    const route = useRoute();
    const router = useRouter();
    const { user, requiresAuthentication } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();
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

    // Logout Handler
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

    // Menu Items List
    const menuItems = ref([
        {
            label: 'Home',
            icon: 'pi pi-home',
            routeName: 'home',
            command: () => router.push({ name: 'home' }),
        },
        {
            label: 'Service',
            icon: 'pi pi-cog',
            routeName: 'contact',
            visible: hasServiceManagement,
            command: () => router.push({ name: 'contact' }),
        },
        {
            label: 'Incidents',
            icon: 'pi pi-exclamation-triangle',
        },
        {
            label: 'Knowledge Base',
            icon: 'pi pi-book',
        },
        {
            label: 'Assets',
            icon: 'pi pi-desktop',
        },
        {
            label: 'Licenses',
            icon: 'pi pi-key',
        },
        {
            label: 'Tasks',
            icon: 'pi pi-check',
        },
    ]);

    // Show Only Visible Menu Items
    const visibleMenuItems = computed(() => menuItems.value.filter((item) => item.visible !== false));

    // Active Menu Logic
    const isActive = (item) => {
        return route.name === item.routeName;
    };

    // Drawer State
    const drawerVisible = ref(false);
    const toggleDrawer = () => {
        drawerVisible.value = !drawerVisible.value;
    };

    // Panel Menu Items (for Drawer)
    const panelMenuItems = computed(() => {
        return visibleMenuItems.value.map((item) => ({
            label: item.label,
            icon: item.icon,
            command: item.command,
        }));
    });

    const showSearchBar = computed(() => route.name === 'view-article');
</script>

<style scoped>
    /* Prevent wrapping */
    .gap-6 > * {
        white-space: nowrap;
    }

    /* Active Menu Gradient Text */
    .text-transparent.bg-clip-text {
        -webkit-background-clip: text;
        background-clip: text;
    }
</style>
