<script setup>
    const props = defineProps({
        loading: {
            type: Boolean,
            default: false,
        },
    });
</script>
<template>
    <div v-if="loading" class="flex flex-col gap-y-8">
        <div>
            <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div
                    v-for="n in 4"
                    :key="'active-skel-' + n"
                    class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                >
                    <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
            </div>
        </div>

        <div>
            <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div
                    v-for="n in 4"
                    :key="'expired-skel-' + n"
                    class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                >
                    <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
            </div>
        </div>
    </div>
</template>
