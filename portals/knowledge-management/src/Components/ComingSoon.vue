<template>
    <div class="bg-gray-50 flex flex-col overflow-hidden" style="height: calc(100vh - 160px)">
        <div class="flex-grow flex items-center justify-center overflow-hidden">
            <div class="w-full max-w-screen-xl mx-auto px-4">
                <div class="p-4 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">Coming Soon!</h1>
                    <p class="text-lg text-gray-600">
                        This feature is currently under development and will be available soon.
                    </p>
                    <button
                        @click="goHome"
                        class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded shadow text-white bg-gradient-to-br from-brand-500 to-brand-800 hover:from-brand-600 hover:to-brand-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                    >
                        <HomeIcon class="h-5 w-5 mr-1" />
                        Return to Home
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { HomeIcon } from '@heroicons/vue/24/outline';
    import { useRouter } from 'vue-router';
    const router = useRouter();
    const goHome = () => {
        router.push({ name: 'home' });
    };
</script>
