<script setup>
    import { XMarkIcon } from '@heroicons/vue/24/outline/index.js';
</script>

<template>
    <div class="px-6 py-12">
        <div class="mx-auto grid max-w-lg justify-items-center text-center">
            <div class="mb-4 rounded-full bg-brand-100 p-3 dark:bg-brand-500/20">
                <XMarkIcon class="h-6 w-6 text-brand-500 dark:text-brand-400" />
            </div>

            <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                <slot name="heading" />
            </h4>

            <p v-if="$slots.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                <slot name="description" />
            </p>

            <div v-if="$slots.actions" class="mt-6">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
