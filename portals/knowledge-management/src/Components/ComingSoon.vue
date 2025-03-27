<script setup>
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const goHome = () => {
        router.push({ name: 'home' });
    };
</script>

<template>
    <div class="w-full flex flex-col items-center bg-gray-50 overflow-x-hidden">
        <div class="max-w-screen-xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Coming Soon</h1>
                <p class="mt-4 text-lg leading-6 text-gray-600">
                    This feature is currently under development and will be available soon.
                </p>
                <div class="mt-10">
                    <button
                        @click="goHome"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-br from-brand-500 to-brand-800 hover:from-brand-600 hover:to-brand-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                    >
                        Return to Home
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
