<script setup>
    import { defineProps } from 'vue';

    const props = defineProps({
        content: {
            type: String,
            required: true,
        },
    });
</script>

<template>
    <div v-html="content"></div>
</template>

<style scoped></style>
