<script setup>
    import { defineProps } from 'vue';

    defineProps({
        value: {
            type: String,
            required: true,
        },
    });
</script>

<template>
    <span
        class="flex items-center justify-center gap-x-1 rounded ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 bg-gray-100 ring-gray-600/10"
    >
        {{ value }}
    </span>
</template>
