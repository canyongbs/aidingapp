<script setup>
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    
    const { user } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();
</script>

<template>
    <div class="top-0 z-40 flex flex-col items-center bg-gray-50">
        <div class="bg-gradient-to-br from-brand-500 to-brand-800 w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div class="text-right" v-if="hasServiceManagement && user">
                    <router-link :to="{ name: 'create-service-request' }">
                        <button class="px-3 py-2 font-medium text-sm rounded bg-white text-brand-700 dark:text-brand-400">
                            New Request
                        </button>
                    </router-link>
                </div>

                <div class="flex flex-col gap-y-1 text-left">
                    <h3 class="text-3xl font-semibold text-white"><slot name="heading" /></h3>
                    <p class="text-brand-100"><slot name="description" /></p>
                </div>

                <slot name="belowHeaderContent" />
            </div>
        </div>
    </div>

    <main class="xl:px-6 bg-gray-50">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <div class="ring-1 ring-black/5 shadow-sm xl:-mx-6 px-6 py-4 xl:rounded bg-white">
                <slot />
            </div>
        </div>
    </main>
</template>