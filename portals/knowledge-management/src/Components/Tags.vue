<script setup>
    import { defineProps } from 'vue';
    import Badge from './Badge.vue';

    defineProps({
        tags: {
            type: Array,
            required: true,
        },
    });
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <Badge v-for="tag in tags" :key="tag.id" :value="tag.name" />
    </div>
</template>

<style scoped></style>
